use crate::common::extract_exprs;
use crate::exception::ExtResult;
use crate::lazy_frame::PhpLazyFrame;
use ext_php_rs::prelude::*;
use ext_php_rs::types::ZendHashTable;
use polars::prelude::LazyFrame;
use polars::lazy::dsl::{all, Expr};

/// Create a wildcard Expr that matches all columns
fn all_expr() -> Expr {
    Expr::from(all())
}

#[php_class]
#[php(name = "Polars\\LazyGroupBy")]
#[derive(Clone)]
pub struct PhpLazyGroupBy {
    lf: LazyFrame,
    by: Vec<Expr>,
}

impl PhpLazyGroupBy {
    pub fn new(lf: LazyFrame, by: Vec<Expr>) -> Self {
        Self { lf, by }
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
        let gb = self.lf.clone().group_by(self.by.clone());
        Ok(PhpLazyFrame {
            inner: gb.agg(&exprs),
        })
    }

    /// Count rows per group
    /// @return \Polars\LazyFrame
    pub fn count(&self) -> PhpLazyFrame {
        let gb = self.lf.clone().group_by(self.by.clone());
        PhpLazyFrame {
            inner: gb.agg(&[all_expr().count()]),
        }
    }

    /// First row per group
    /// @return \Polars\LazyFrame
    pub fn first(&self) -> PhpLazyFrame {
        let gb = self.lf.clone().group_by(self.by.clone());
        PhpLazyFrame {
            inner: gb.agg(&[all_expr().first()]),
        }
    }

    /// Last row per group
    /// @return \Polars\LazyFrame
    pub fn last(&self) -> PhpLazyFrame {
        let gb = self.lf.clone().group_by(self.by.clone());
        PhpLazyFrame {
            inner: gb.agg(&[all_expr().last()]),
        }
    }

    /// First n rows per group
    /// @return \Polars\LazyFrame
    #[php(defaults(n = 5))]
    pub fn head(&self, n: i64) -> PhpLazyFrame {
        let gb = self.lf.clone().group_by(self.by.clone());
        PhpLazyFrame {
            inner: gb.head(Some(n as usize)),
        }
    }

    /// Last n rows per group
    /// @return \Polars\LazyFrame
    #[php(defaults(n = 5))]
    pub fn tail(&self, n: i64) -> PhpLazyFrame {
        let gb = self.lf.clone().group_by(self.by.clone());
        PhpLazyFrame {
            inner: gb.tail(Some(n as usize)),
        }
    }

    /// Sum per group
    /// @return \Polars\LazyFrame
    pub fn sum(&self) -> PhpLazyFrame {
        let gb = self.lf.clone().group_by(self.by.clone());
        PhpLazyFrame {
            inner: gb.agg(&[all_expr().sum()]),
        }
    }

    /// Mean per group
    /// @return \Polars\LazyFrame
    pub fn mean(&self) -> PhpLazyFrame {
        let gb = self.lf.clone().group_by(self.by.clone());
        PhpLazyFrame {
            inner: gb.agg(&[all_expr().mean()]),
        }
    }

    /// Median per group
    /// @return \Polars\LazyFrame
    pub fn median(&self) -> PhpLazyFrame {
        let gb = self.lf.clone().group_by(self.by.clone());
        PhpLazyFrame {
            inner: gb.agg(&[all_expr().median()]),
        }
    }

    /// Min per group
    /// @return \Polars\LazyFrame
    pub fn min(&self) -> PhpLazyFrame {
        let gb = self.lf.clone().group_by(self.by.clone());
        PhpLazyFrame {
            inner: gb.agg(&[all_expr().min()]),
        }
    }

    /// Max per group
    /// @return \Polars\LazyFrame
    pub fn max(&self) -> PhpLazyFrame {
        let gb = self.lf.clone().group_by(self.by.clone());
        PhpLazyFrame {
            inner: gb.agg(&[all_expr().max()]),
        }
    }
}
