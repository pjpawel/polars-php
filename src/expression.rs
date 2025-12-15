use ext_php_rs::{php_class, php_impl};
use polars_plan::dsl::{all, col, cols, Expr};

#[php_class]
#[php(name = "Polars\\Expr")]
#[derive(Clone)]
pub struct PolarsExpr(Expr);

#[php_impl]
impl PolarsExpr {

    /// Constructor calls col static method
    pub fn __construct(name: String) -> Self {
        Self::col(name)
    }

    pub fn col(name: String) -> Self {
        Self(col(name))
    }

    pub fn cols(names: Vec<String>) -> Self {
        Self(cols(names).as_expr())
    }

    pub fn all() -> Self {
        Self(all().as_expr())
    }

    pub fn eq(&self, other: &PolarsExpr) -> Self {
        self.0.clone().eq(other.clone()).into()
    }

    

}

/// Methods that are hidden from PHP stubs
impl PolarsExpr {

    pub fn get_expr(&self) -> &Expr {
        &self.0
    }

}

// impl Deref for PolarsExpr {
//     type Target = Expr;
//     fn deref(&self) -> &Self::Target {
//         &self.0
//     }
// }

impl Into<PolarsExpr> for Expr {
    fn into(self) -> PolarsExpr {
        PolarsExpr(self)
    }
}

impl Into<Expr> for PolarsExpr {
    fn into(self) -> Expr {
        self.0
    }
}

// impl Into<Expr> for &PolarsExpr {
//     fn into(self) -> Expr {
//         self.clone().0
//     }
// }


