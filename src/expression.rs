use std::ops::{Add, Div, Neg};
use ext_php_rs::{php_class, php_enum, php_impl};
use ext_php_rs::flags::DataType;
use ext_php_rs::types::{ZendObject, Zval};
use polars::prelude::ClosedInterval;
use polars::lazy::dsl::{all, col, cols, Expr, lit};
use polars::prelude::{Literal, NULL};
use crate::exception::{ExtResult, PolarsException};

#[php_class]
#[php(name = "Polars\\Expr")]
#[derive(Clone, Debug)]
pub struct PolarsExpr(Expr);

#[php_impl]
impl PolarsExpr {

    /// Constructor creates LiteralValue from int, float, string, boolean, or null. Passing other values will cause throwing exception
    /// @throws Polars\Exception
    pub fn __construct(value: &Zval) -> ExtResult<Self> {
        Ok(zval_to_expr(value)?.into())
    }

    pub fn col(name: String) -> Self {
        Self(col(name))
    }

    pub fn cols(names: Vec<String>) -> Self {
        Self(cols(names).as_expr())
    }

    // AGGREGATIONS //
    pub fn all() -> Self {
        Self(all().as_expr())
    }

    #[allow(non_snake_case)]
    #[php(defaults(ignoreNulls = true), optional = ignoreNulls)]
    pub fn any(&self, ignoreNulls: bool) -> Self {
        self.0.clone().any(ignoreNulls).into()
    }

    pub fn count(&self) -> Self {
        self.0.clone().count().into()
    }

    pub fn first(&self) -> Self {
        self.0.clone().first().into()
    }

    pub fn last(&self) -> Self {
        self.0.clone().last().into()
    }

    pub fn len(&self) -> Self {
        self.0.clone().len().into()
    }

    pub fn max(&self) -> Self {
        self.0.clone().max().into()
    }

    pub fn mean(&self) -> Self {
        self.0.clone().mean().into()
    }

    pub fn median(&self) -> Self {
        self.0.clone().median().into()
    }

    pub fn min(&self) -> Self {
        self.0.clone().min().into()
    }

    #[php(name="nUnique")]
    pub fn n_unique(&self) -> Self {
        self.0.clone().n_unique().into()
    }

    #[php(name="nanMax")]
    pub fn nan_max(&self) -> Self {
        self.0.clone().nan_max().into()
    }

    #[php(name="nanMin")]
    pub fn nan_min(&self) -> Self {
        self.0.clone().nan_min().into()
    }

    #[php(name="nullCount")]
    pub fn null_count(&self) -> Self {
        self.0.clone().null_count().into()
    }

    pub fn product(&self) -> Self {
        self.0.clone().product().into()
    }

    // pub fn quantile(&self) -> Self {
    //     self.0.clone().product().into()
    // }

    #[php(defaults(ddof = 1))]
    pub fn std(&self, ddof: u8) ->Self {
        self.0.clone().std(ddof).into()
    }

    pub fn sum(&self) -> Self {
        self.0.clone().sum().into()
    }

    #[php(defaults(ddof = 1))]
    pub fn variance(&self, ddof: u8) -> Self {
        self.0.clone().var(ddof).into()
    }

    // OPERATORS //

    /// @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
    pub fn eq(&self, other: &Zval) -> ExtResult<Self> {
        let other_expr = zval_to_expr(other)?;
        Ok(self.0.clone().eq(other_expr).into())
    }

    /// @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
    #[php(name="eqMissing")]
    pub fn eq_missing(&self, other: &Zval) -> ExtResult<Self> {
        let other_expr = zval_to_expr(other)?;
        Ok(self.0.clone().eq_missing(other_expr).into())
    }

    /// @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
    pub fn ge(&self, other: &Zval) -> ExtResult<Self> {
        let other_expr = zval_to_expr(other)?;
        Ok(self.0.clone().gt_eq(other_expr).into())
    }

    /// @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
    pub fn gt(&self, other: &Zval) -> ExtResult<Self> {
        let other_expr = zval_to_expr(other)?;
        Ok(self.0.clone().gt(other_expr).into())
    }

    /// @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
    pub fn le(&self, other: &Zval) -> ExtResult<Self> {
        let other_expr = zval_to_expr(other)?;
        Ok(self.0.clone().lt_eq(other_expr).into())
    }

    /// @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
    pub fn lt(&self, other: &Zval) -> ExtResult<Self> {
        let other_expr = zval_to_expr(other)?;
        Ok(self.0.clone().lt(other_expr).into())
    }

    /// @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
    pub fn ne(&self, other: &Zval) -> ExtResult<Self> {
        let other_expr = zval_to_expr(other)?;
        Ok(self.0.clone().neq(other_expr).into())
    }

    /// @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
    #[php(name="neqMissing")]
    pub fn neq_missing(&self, other: &Zval) -> ExtResult<Self> {
        let other_expr = zval_to_expr(other)?;
        Ok(self.0.clone().neq_missing(other_expr).into())
    }

    /// @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
    pub fn add(&self, other: &Zval) -> ExtResult<Self> {
        let other_expr = zval_to_expr(other)?;
        Ok(self.0.clone().add(other_expr).into())
    }

    /// @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
    #[php(name="floorDiv")]
    pub fn floor_div(&self, other: &Zval) -> ExtResult<Self> {
        let other_expr = zval_to_expr(other)?;
        Ok(self.0.clone().floor_div(other_expr).into())
    }

    /// @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
    pub fn modulo(&self, other: &Zval) -> ExtResult<Self> {
        let other_expr = zval_to_expr(other)?;
        Ok((self.0.clone() % other_expr).into())
    }

    /// @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
    pub fn mul(&self, other: &Zval) -> ExtResult<Self> {
        let other_expr = zval_to_expr(other)?;
        Ok((self.0.clone() * other_expr).into())
    }

    pub fn neg(&self) -> Self {
        self.0.clone().neg().into()
    }

    /// @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
    pub fn pow(&self, other: &Zval) -> ExtResult<Self> {
        let other_expr = zval_to_expr(other)?;
        Ok(self.0.clone().pow(other_expr).into())
    }

    /// @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
    pub fn sub(&self, other: &Zval) -> ExtResult<Self> {
        let other_expr = zval_to_expr(other)?;
        Ok((self.0.clone() - other_expr).into())
    }

    /// @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
    pub fn div(&self, other: &Zval) -> ExtResult<Self> {
        let other_expr = zval_to_expr(other)?;
        Ok(self.0.clone().div(other_expr).into())
    }

    /// @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
    pub fn xxor(&self, other: &Zval) -> ExtResult<Self> {
        let other_expr = zval_to_expr(other)?;
        Ok(self.0.clone().xor(other_expr).into())
    }

    // BOOLEAN //

    #[php(name="hasNulls")]
    pub fn has_nulls(&self) -> Self {
        self.0.clone().null_count().gt(0).into()
    }

    #[allow(non_snake_case)]
    #[php(name="isBetween")]
    pub fn is_between(
        &self,
        lowerBound: &Zval,
        upperBound: &Zval,
        closed: PolarsClosedInterval
    ) -> ExtResult<Self> {
        let lower = zval_to_expr(lowerBound)?;
        let upper = zval_to_expr(upperBound)?;
        Ok(self.0.clone()
            .is_between(lower, upper, closed.into())
            .into())
    }


}

/// Methods that are hidden from PHP stubs
impl PolarsExpr {

    pub fn get_expr(&self) -> &Expr {
        &self.0
    }

}

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

impl Into<Expr> for &PolarsExpr {
    fn into(self) -> Expr {
        self.clone().0
    }
}

// impl FromZval<'_> for PolarsExpr {
//     const TYPE: DataType = DataType::Object(Some("Polars\\Expr"));
//
//     fn from_zval(zval: &Zval) -> Option<Self> {
//         //let object: &ZendObject = value.object().unwrap();
//         //                 if !object.is_instance::<PolarsExpr>() {
//         //                     return Err(PolarsException::new("Passed object is not of class Polars\\Expr".to_string()));
//         //                 }
//         //                 value.extract::<&PolarsExpr>().unwrap().into()
//     }
// }

#[php_enum]
#[php(name = "Polars\\ClosedInterval")]
pub enum PolarsClosedInterval {
    Both,
    Left,
    Right,
    None,
}

impl Into<ClosedInterval> for PolarsClosedInterval {
    fn into(self) -> ClosedInterval {
        match self {
            PolarsClosedInterval::Both => ClosedInterval::Both,
            PolarsClosedInterval::Left => ClosedInterval::Left,
            PolarsClosedInterval::Right => ClosedInterval::Right,
            PolarsClosedInterval::None => ClosedInterval::None,
        }
    }
}

// #[derive(Clone, Debug, ZvalConvert)]
// enum IntoExprUnion<'a> {
//     Str(&'a str),
//     Long(u64),
//     Double(f64),
//     Bool(bool),
//     None,
//     Expr(&'a PolarsExpr),
// }
//
// impl IntoExprUnion<'_> {
//     pub fn get_expr(&self) -> Expr {
//         match self {
//             Self::Str(name) => (*name).into(),
//             Self::Long(number) => (*number).into(),
//             Self::Double(number) => (*number).into(),
//             Self::Bool(boolean) => (*boolean).into(),
//             Self::None => NULL.lit(),
//             Self::Expr(p_expr) => p_expr.get_expr().to_owned()
//         }
//     }
// }

pub fn zval_to_expr(value: &Zval) -> ExtResult<Expr> {
    Ok(
        match value.get_type() {
            DataType::Long => lit(value.long().unwrap() as i64),
            DataType::Double => lit(value.double().unwrap()),
            DataType::String => lit(value.str().unwrap()),
            DataType::Bool | DataType::False | DataType::True => lit(value.bool().unwrap()),
            DataType::Null => NULL.lit(),
            // DataType::Object("Polars\\Expr") => value.object().unwrap().,
            DataType::Object(_) => {
                let object: &ZendObject = value.object().unwrap();
                if !object.is_instance::<PolarsExpr>() {
                    return Err(PolarsException::new("Passed object is not of class Polars\\Expr".to_string()));
                }
                value.extract::<&PolarsExpr>().unwrap().into()
            },
            default => {
                return Err(PolarsException::new("Cannot convert variable to expression. Possible values are: int, float, string, boolean, or null.".to_string()))
            }
        }
    )
}
