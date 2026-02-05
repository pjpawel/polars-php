use crate::common::extract_exprs;
use crate::data_frame::PhpDataFrame;
use crate::data_type::PolarsDataType;
use crate::exception::{ExtResult, PolarsException};
use crate::expression::PolarsExpr;
use crate::lazy_group_by::PhpLazyGroupBy;
use ext_php_rs::prelude::*;
use ext_php_rs::types::{ZendHashTable, Zval};
use polars::prelude::{
    JoinArgs, JoinType, LazyFrame, Selector, SortMultipleOptions, UniqueKeepStrategy,
};

#[php_class]
#[php(name = "Polars\\LazyFrame")]
#[derive(Clone)]
pub struct PhpLazyFrame {
    pub inner: LazyFrame,
}

#[php_impl]
#[php(change_method_case = "camelCase")]
impl PhpLazyFrame {
    // Core //

    /// Execute the lazy query and return a DataFrame
    /// @return \Polars\DataFrame
    pub fn collect(&self) -> ExtResult<PhpDataFrame> {
        let df = self
            .inner
            .clone()
            .collect()
            .map_err(|e| PolarsException::new(format!("Failed to collect LazyFrame: {}", e)))?;
        Ok(df.into())
    }

    /// Select columns by expression
    /// @param \Polars\Expr[] $expressions
    /// @return \Polars\LazyFrame
    pub fn select(&self, expressions: &ZendHashTable) -> ExtResult<Self> {
        let exprs = extract_exprs(expressions)?;
        Ok(Self {
            inner: self.inner.clone().select(&exprs),
        })
    }

    /// Filter rows by expression
    /// @return \Polars\LazyFrame
    pub fn filter(&self, expression: &PolarsExpr) -> Self {
        Self {
            inner: self.inner.clone().filter(expression.get_expr().clone()),
        }
    }

    /// Add or modify columns
    /// @param \Polars\Expr[] $expressions
    /// @return \Polars\LazyFrame
    #[php(name = "withColumns")]
    pub fn with_columns(&self, expressions: &ZendHashTable) -> ExtResult<Self> {
        let exprs = extract_exprs(expressions)?;
        Ok(Self {
            inner: self.inner.clone().with_columns(&exprs),
        })
    }

    /// Group by expressions
    /// @param \Polars\Expr[] $expressions
    /// @return \Polars\LazyGroupBy
    #[php(name = "groupBy")]
    pub fn group_by(&self, expressions: &ZendHashTable) -> ExtResult<PhpLazyGroupBy> {
        let exprs = extract_exprs(expressions)?;
        Ok(PhpLazyGroupBy::new(self.inner.clone(), exprs))
    }

    /// Sort by a single column
    /// @return \Polars\LazyFrame
    #[allow(non_snake_case)]
    #[php(defaults(descending = false, nullsLast = true))]
    pub fn sort(&self, column: String, descending: bool, nullsLast: bool) -> Self {
        let opts = SortMultipleOptions::new()
            .with_order_descending(descending)
            .with_nulls_last(nullsLast);
        Self {
            inner: self.inner.clone().sort([&column], opts),
        }
    }

    // Attributes //

    /// Get column names
    /// @return string[]
    #[php(getter)]
    pub fn get_columns(&mut self) -> ExtResult<Vec<String>> {
        let schema = self
            .inner
            .collect_schema()
            .map_err(|e| PolarsException::new(format!("Failed to get schema: {}", e)))?;
        Ok(schema.iter_names().map(|n| n.to_string()).collect())
    }

    /// Get data types
    /// @return \Polars\DataType[]
    #[php(getter)]
    pub fn dtypes(&mut self) -> ExtResult<Vec<PolarsDataType>> {
        let schema = self
            .inner
            .collect_schema()
            .map_err(|e| PolarsException::new(format!("Failed to get schema: {}", e)))?;
        Ok(schema
            .iter_values()
            .map(|d| d.clone().into())
            .collect())
    }

    /// Get number of columns
    /// @return int
    pub fn width(&mut self) -> ExtResult<usize> {
        let schema = self
            .inner
            .collect_schema()
            .map_err(|e| PolarsException::new(format!("Failed to get schema: {}", e)))?;
        Ok(schema.len())
    }

    /// Get schema description as string
    /// @return string
    pub fn schema(&mut self) -> ExtResult<String> {
        let schema = self
            .inner
            .collect_schema()
            .map_err(|e| PolarsException::new(format!("Failed to get schema: {}", e)))?;
        Ok(format!("{:?}", schema))
    }

    // Row Operations //

    /// Get the first n rows
    /// @return \Polars\LazyFrame
    #[php(defaults(n = 10))]
    pub fn head(&self, n: i64) -> Self {
        Self {
            inner: self.inner.clone().limit(n as u32),
        }
    }

    /// Get the last n rows
    /// @return \Polars\LazyFrame
    #[php(defaults(n = 10))]
    pub fn tail(&self, n: i64) -> Self {
        Self {
            inner: self.inner.clone().tail(n as u32),
        }
    }

    /// Get the first row
    /// @return \Polars\LazyFrame
    pub fn first(&self) -> Self {
        Self {
            inner: self.inner.clone().first(),
        }
    }

    /// Get the last row
    /// @return \Polars\LazyFrame
    pub fn last(&self) -> Self {
        Self {
            inner: self.inner.clone().last(),
        }
    }

    /// Get a slice of rows
    /// @return \Polars\LazyFrame
    pub fn slice(&self, offset: i64, length: i64) -> Self {
        Self {
            inner: self.inner.clone().slice(offset, length as u32),
        }
    }

    /// Limit to n rows (alias for head)
    /// @return \Polars\LazyFrame
    #[php(defaults(n = 10))]
    pub fn limit(&self, n: i64) -> Self {
        self.head(n)
    }

    // Aggregations //

    /// Return the number of non-null elements for each column
    /// @return \Polars\LazyFrame
    pub fn count(&self) -> Self {
        Self {
            inner: self.inner.clone().count(),
        }
    }

    /// Aggregate the columns to their sum value
    /// @return \Polars\LazyFrame
    pub fn sum(&self) -> Self {
        Self {
            inner: self.inner.clone().sum(),
        }
    }

    /// Aggregate the columns to their mean value
    /// @return \Polars\LazyFrame
    pub fn mean(&self) -> Self {
        Self {
            inner: self.inner.clone().mean(),
        }
    }

    /// Aggregate the columns to their median value
    /// @return \Polars\LazyFrame
    pub fn median(&self) -> Self {
        Self {
            inner: self.inner.clone().median(),
        }
    }

    /// Aggregate the columns to their minimum value
    /// @return \Polars\LazyFrame
    pub fn min(&self) -> Self {
        Self {
            inner: self.inner.clone().min(),
        }
    }

    /// Aggregate the columns to their maximum value
    /// @return \Polars\LazyFrame
    pub fn max(&self) -> Self {
        Self {
            inner: self.inner.clone().max(),
        }
    }

    /// Aggregate the columns to their standard deviation
    /// @return \Polars\LazyFrame
    #[php(defaults(ddof = 0))]
    pub fn std(&self, ddof: u8) -> Self {
        Self {
            inner: self.inner.clone().std(ddof),
        }
    }

    /// Aggregate the columns to their variance
    /// @return \Polars\LazyFrame
    #[php(defaults(ddof = 0))]
    pub fn variance(&self, ddof: u8) -> Self {
        Self {
            inner: self.inner.clone().var(ddof),
        }
    }

    /// Aggregate the columns to their quantile value
    /// @return \Polars\LazyFrame
    pub fn quantile(&self, quantile: f64) -> Self {
        use polars::prelude::QuantileMethod;
        Self {
            inner: self
                .inner
                .clone()
                .quantile(polars_plan::dsl::lit(quantile), QuantileMethod::Nearest),
        }
    }

    /// Aggregate the columns to their null count
    /// @return \Polars\LazyFrame
    #[php(name = "nullCount")]
    pub fn null_count(&self) -> Self {
        Self {
            inner: self.inner.clone().null_count(),
        }
    }

    // Column Manipulation //

    /// Drop columns
    /// @param string[] $columns
    /// @return \Polars\LazyFrame
    pub fn drop(&self, columns: Vec<String>) -> Self {
        use polars::prelude::PlSmallStr;
        let names: Vec<PlSmallStr> = columns.into_iter().map(PlSmallStr::from).collect();
        let selector = Selector::ByName {
            names: names.into(),
            strict: false,
        };
        Self {
            inner: self.inner.clone().drop(selector),
        }
    }

    /// Rename columns
    /// @param string[] $existing Old column names
    /// @param string[] $newNames New column names
    /// @return \Polars\LazyFrame
    #[allow(non_snake_case)]
    pub fn rename(&self, existing: Vec<String>, newNames: Vec<String>) -> Self {
        Self {
            inner: self.inner.clone().rename(existing, newNames, true),
        }
    }

    /// Get unique rows
    /// @param string[]|null $subset Column names to consider for uniqueness
    /// @return \Polars\LazyFrame
    #[php(defaults(keep = "first".to_string()))]
    pub fn unique(&self, subset: Option<Vec<String>>, keep: String) -> ExtResult<Self> {
        use polars::prelude::PlSmallStr;
        let strategy = match keep.as_str() {
            "first" => UniqueKeepStrategy::First,
            "last" => UniqueKeepStrategy::Last,
            "any" => UniqueKeepStrategy::Any,
            "none" => UniqueKeepStrategy::None,
            _ => {
                return Err(PolarsException::new(format!(
                    "Invalid keep strategy: {}. Use 'first', 'last', 'any', or 'none'",
                    keep
                )));
            }
        };
        let selector = subset.map(|cols| {
            let names: Vec<PlSmallStr> = cols.into_iter().map(PlSmallStr::from).collect();
            Selector::ByName {
                names: names.into(),
                strict: false,
            }
        });
        Ok(Self {
            inner: self.inner.clone().unique(selector, strategy),
        })
    }

    // Null Handling //

    /// Drop rows with null values
    /// @param string[]|null $subset Column names to check
    /// @return \Polars\LazyFrame
    #[php(name = "dropNulls")]
    pub fn drop_nulls(&self, subset: Option<Vec<String>>) -> Self {
        use polars::prelude::PlSmallStr;
        let selector = subset.map(|cols| {
            let names: Vec<PlSmallStr> = cols.into_iter().map(PlSmallStr::from).collect();
            Selector::ByName {
                names: names.into(),
                strict: false,
            }
        });
        Self {
            inner: self.inner.clone().drop_nulls(selector),
        }
    }

    /// Fill null values with a value or expression
    /// @param int|float|string|bool|null|\Polars\Expr $value
    /// @return \Polars\LazyFrame
    #[php(name = "fillNull")]
    pub fn fill_null(&self, value: &Zval) -> ExtResult<Self> {
        let expr = crate::expression::zval_to_expr(value)?;
        Ok(Self {
            inner: self.inner.clone().fill_null(expr),
        })
    }

    /// Fill NaN values with a value or expression
    /// @param int|float|string|bool|null|\Polars\Expr $value
    /// @return \Polars\LazyFrame
    #[php(name = "fillNan")]
    pub fn fill_nan(&self, value: &Zval) -> ExtResult<Self> {
        let expr = crate::expression::zval_to_expr(value)?;
        Ok(Self {
            inner: self.inner.clone().fill_nan(expr),
        })
    }

    // Join //

    /// Join with another LazyFrame
    /// @param \Polars\LazyFrame $other The right LazyFrame
    /// @param \Polars\Expr[] $on Join columns (used for both left and right)
    /// @param string $how Join type: 'inner', 'left', 'right', 'full', 'cross'
    /// @return \Polars\LazyFrame
    #[php(defaults(how = "inner".to_string()))]
    pub fn join(&self, other: &PhpLazyFrame, on: &ZendHashTable, how: String) -> ExtResult<Self> {
        let exprs = extract_exprs(on)?;
        let join_type = match how.as_str() {
            "inner" => JoinType::Inner,
            "left" => JoinType::Left,
            "right" => JoinType::Right,
            "full" => JoinType::Full,
            "cross" => JoinType::Cross,
            _ => {
                return Err(PolarsException::new(format!(
                    "Invalid join type: {}. Use 'inner', 'left', 'right', 'full', or 'cross'",
                    how
                )));
            }
        };
        let lf = self.inner.clone().join(
            other.inner.clone(),
            &exprs,
            &exprs,
            JoinArgs::new(join_type),
        );
        Ok(Self { inner: lf })
    }

    // Miscellaneous //

    /// Reverse row order
    /// @return \Polars\LazyFrame
    pub fn reverse(&self) -> Self {
        Self {
            inner: self.inner.clone().reverse(),
        }
    }

    /// Return the query plan as a string
    /// @return string
    #[php(defaults(optimized = true))]
    pub fn explain(&self, optimized: bool) -> ExtResult<String> {
        if optimized {
            self.inner
                .describe_optimized_plan()
                .map_err(|e| PolarsException::new(format!("Failed to describe plan: {}", e)))
        } else {
            self.inner
                .describe_plan()
                .map_err(|e| PolarsException::new(format!("Failed to describe plan: {}", e)))
        }
    }

    /// Cache the LazyFrame computation
    /// @return \Polars\LazyFrame
    pub fn cache(&self) -> Self {
        Self {
            inner: self.inner.clone().cache(),
        }
    }

    /// String representation showing the query plan
    #[php(name = "__toString")]
    pub fn __to_string(&self) -> String {
        match self.inner.describe_plan() {
            Ok(plan) => plan,
            Err(e) => format!("LazyFrame (plan unavailable: {})", e),
        }
    }
}

impl From<LazyFrame> for PhpLazyFrame {
    fn from(lf: LazyFrame) -> Self {
        PhpLazyFrame { inner: lf }
    }
}
