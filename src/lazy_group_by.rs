use crate::common::{extract_exprs, parse_dtype};
use crate::data_frame::PhpDataFrame;
use crate::data_type::PolarsDataType;
use crate::exception::{ExtResult, PolarsException};
use crate::expression::{PolarsQuantileMethod, zval_to_expr};
use crate::lazy_frame::PhpLazyFrame;
use ext_php_rs::convert::IntoZval;
use ext_php_rs::prelude::*;
use ext_php_rs::types::{ArrayKey, ZendCallable, ZendClassObject, ZendHashTable, Zval};
use polars::lazy::dsl::{Expr, all};
use polars::prelude::{
    DataFrame, LazyFrame, LazyGroupBy, PlSmallStr, PlanCallback, PolarsResult, Schema, SchemaRef,
    polars_err,
};
use std::sync::{Arc, Mutex};

/// Create a wildcard Expr that matches all columns
fn all_expr() -> Expr {
    Expr::from(all())
}

struct SendCallable(Mutex<ZendCallable<'static>>);
unsafe impl Send for SendCallable {}
unsafe impl Sync for SendCallable {}

impl SendCallable {
    fn try_call(&self, arg: &Zval) -> std::result::Result<Zval, String> {
        let guard = self
            .0
            .lock()
            .map_err(|_| "mapGroups callback lock poisoned".to_string())?;
        guard.try_call(vec![arg]).map_err(|e| format!("{e:?}"))
    }
}

/// Build a Polars Schema from a PHP array of column name => dtype (string name or \Polars\DataType)
fn hashtable_to_schema(schema: &ZendHashTable) -> ExtResult<SchemaRef> {
    let mut out = Schema::with_capacity(schema.len());
    for (key, value) in schema.iter() {
        let name = match &key {
            ArrayKey::String(s) => s.clone(),
            ArrayKey::Str(s) => s.to_string(),
            ArrayKey::Long(i) => i.to_string(),
            _ => key.to_string(),
        };
        let dtype = if let Some(s) = value.string() {
            parse_dtype(&s)?
        } else if let Some(dt) = value.extract::<&PolarsDataType>() {
            dt.inner_dtype()
        } else {
            return Err(PolarsException::new(
                "Schema values must be dtype name strings or \\Polars\\DataType objects"
                    .to_string(),
            ));
        };
        out.insert(PlSmallStr::from_string(name), dtype);
    }
    Ok(Arc::new(out))
}

#[php_class]
#[php(name = "Polars\\LazyGroupBy")]
#[derive(Clone)]
pub struct PhpLazyGroupBy {
    lf: LazyFrame,
    by: Vec<Expr>,
    predicates: Vec<Expr>,
}

impl PhpLazyGroupBy {
    pub fn new(lf: LazyFrame, by: Vec<Expr>) -> Self {
        Self {
            lf,
            by,
            predicates: Vec::new(),
        }
    }

    fn group_by(&self) -> LazyGroupBy {
        let mut gb = self.lf.clone().group_by(self.by.clone());
        for predicate in &self.predicates {
            gb = gb.having(predicate.clone());
        }
        gb
    }
}

#[php_impl]
#[php(change_method_case = "camelCase")]
impl PhpLazyGroupBy {
    /// Aggregate using expressions
    /// @param \Polars\Expr[] $expressions
    /// @return \Polars\LazyFrame
    pub fn agg(&self, expressions: &ZendHashTable) -> ExtResult<PhpLazyFrame> {
        let exprs = extract_exprs(expressions)?;
        let gb = self.group_by();
        Ok(PhpLazyFrame {
            inner: gb.agg(&exprs),
        })
    }

    /// Aggregate all values of each group into lists
    /// @return \Polars\LazyFrame
    pub fn all(&self) -> PhpLazyFrame {
        let gb = self.group_by();
        PhpLazyFrame {
            inner: gb.agg(&[all_expr()]),
        }
    }

    /// Count rows per group
    /// @return \Polars\LazyFrame
    pub fn count(&self) -> PhpLazyFrame {
        let gb = self.group_by();
        PhpLazyFrame {
            inner: gb.agg(&[all_expr().count()]),
        }
    }

    /// First row per group
    /// @return \Polars\LazyFrame
    pub fn first(&self) -> PhpLazyFrame {
        let gb = self.group_by();
        PhpLazyFrame {
            inner: gb.agg(&[all_expr().first()]),
        }
    }

    /// Filter groups with predicates evaluated on the aggregated groups; can be chained
    /// @param \Polars\Expr[] $predicates
    /// @return \Polars\LazyGroupBy
    pub fn having(&self, predicates: &ZendHashTable) -> ExtResult<PhpLazyGroupBy> {
        let exprs = extract_exprs(predicates)?;
        let mut result = self.clone();
        result.predicates.extend(exprs);
        Ok(result)
    }

    /// First n rows per group
    /// @return \Polars\LazyFrame
    #[php(defaults(n = 5))]
    pub fn head(&self, n: i64) -> PhpLazyFrame {
        let gb = self.group_by();
        PhpLazyFrame {
            inner: gb.head(Some(n as usize)),
        }
    }

    /// Last row per group
    /// @return \Polars\LazyFrame
    pub fn last(&self) -> PhpLazyFrame {
        let gb = self.group_by();
        PhpLazyFrame {
            inner: gb.agg(&[all_expr().last()]),
        }
    }

    /// Return the number of rows in each group
    /// @param string|null $name Assign a name to the resulting column; if unset, defaults to "len"
    /// @return \Polars\LazyFrame
    pub fn len(&self, name: Option<String>) -> PhpLazyFrame {
        let gb = self.group_by();
        let expr = polars::lazy::dsl::len();
        let expr = match name {
            Some(name) => expr.alias(name),
            None => expr,
        };
        PhpLazyFrame {
            inner: gb.agg(&[expr]),
        }
    }

    /// Apply a custom function over each group as a new DataFrame
    /// @param callable $function Function that receives a \Polars\DataFrame per group and must return a \Polars\DataFrame
    /// @param array<string, string|\Polars\DataType>|null $schema Output schema; if null the input schema is assumed unchanged
    /// @return \Polars\LazyFrame
    /// @throws Polars\Exception
    pub fn map_groups(
        &self,
        function: &Zval,
        schema: Option<&ZendHashTable>,
    ) -> ExtResult<PhpLazyFrame> {
        if !self.predicates.is_empty() {
            return Err(PolarsException::new(
                "mapGroups cannot be combined with having() predicates".to_string(),
            ));
        }
        let callable: ZendCallable<'static> = ZendCallable::new_owned(function.shallow_clone())
            .map_err(|_| {
                PolarsException::new("mapGroups expects a callable as first argument".to_string())
            })?;
        let callable = SendCallable(Mutex::new(callable));
        let callback = PlanCallback::new(move |df: DataFrame| -> PolarsResult<DataFrame> {
            let arg = ZendClassObject::new(PhpDataFrame { inner: df })
                .into_zval(false)
                .map_err(|e| polars_err!(ComputeError: "failed to pass group to callback: {e}"))?;
            let result = callable
                .try_call(&arg)
                .map_err(|e| polars_err!(ComputeError: "mapGroups callback failed: {e}"))?;
            match result.extract::<&PhpDataFrame>() {
                Some(df) => Ok(df.inner.clone()),
                None => Err(polars_err!(
                    ComputeError: "mapGroups callback must return a \\Polars\\DataFrame"
                )),
            }
        });
        let schema = match schema {
            Some(schema) => hashtable_to_schema(schema)?,
            None => self
                .lf
                .clone()
                .collect_schema()
                .map_err(|e| PolarsException::new(format!("Failed to get schema: {}", e)))?,
        };
        Ok(PhpLazyFrame {
            inner: self.group_by().apply(callback, schema),
        })
    }

    /// Last n rows per group
    /// @return \Polars\LazyFrame
    #[php(defaults(n = 5))]
    pub fn tail(&self, n: i64) -> PhpLazyFrame {
        let gb = self.group_by();
        PhpLazyFrame {
            inner: gb.tail(Some(n as usize)),
        }
    }

    /// Sum per group
    /// @return \Polars\LazyFrame
    pub fn sum(&self) -> PhpLazyFrame {
        let gb = self.group_by();
        PhpLazyFrame {
            inner: gb.agg(&[all_expr().sum()]),
        }
    }

    /// Mean per group
    /// @return \Polars\LazyFrame
    pub fn mean(&self) -> PhpLazyFrame {
        let gb = self.group_by();
        PhpLazyFrame {
            inner: gb.agg(&[all_expr().mean()]),
        }
    }

    /// Median per group
    /// @return \Polars\LazyFrame
    pub fn median(&self) -> PhpLazyFrame {
        let gb = self.group_by();
        PhpLazyFrame {
            inner: gb.agg(&[all_expr().median()]),
        }
    }

    /// Min per group
    /// @return \Polars\LazyFrame
    pub fn min(&self) -> PhpLazyFrame {
        let gb = self.group_by();
        PhpLazyFrame {
            inner: gb.agg(&[all_expr().min()]),
        }
    }

    /// Max per group
    /// @return \Polars\LazyFrame
    pub fn max(&self) -> PhpLazyFrame {
        let gb = self.group_by();
        PhpLazyFrame {
            inner: gb.agg(&[all_expr().max()]),
        }
    }

    /// Count the unique values per group
    /// @return \Polars\LazyFrame
    pub fn n_unique(&self) -> PhpLazyFrame {
        let gb = self.group_by();
        PhpLazyFrame {
            inner: gb.agg(&[all_expr().n_unique()]),
        }
    }

    /// Compute the quantile per group
    /// @param int|float|string|bool|null|\Polars\Expr $quantile Quantile value (0.0 to 1.0)
    /// @return \Polars\LazyFrame
    /// @throws Polars\Exception
    #[php(defaults(interpolation = PolarsQuantileMethod::Nearest))]
    pub fn quantile(
        &self,
        quantile: &Zval,
        interpolation: PolarsQuantileMethod,
    ) -> ExtResult<PhpLazyFrame> {
        let quantile_expr = zval_to_expr(quantile)?;
        let gb = self.group_by();
        Ok(PhpLazyFrame {
            inner: gb.agg(&[all_expr().quantile(quantile_expr, interpolation.into())]),
        })
    }
}
