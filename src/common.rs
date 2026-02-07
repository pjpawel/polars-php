use ext_php_rs::types::{ZendHashTable, Zval};
use polars::prelude::{AnyValue, DataType};
use polars::lazy::dsl::Expr;
use crate::exception::{ExtResult, PolarsException};
use crate::expression::PolarsExpr;

/// Parse a string dtype name to a Polars DataType
pub fn parse_dtype(dtype: &str) -> ExtResult<DataType> {
    match dtype.to_lowercase().as_str() {
        "int8" | "i8" => Ok(DataType::Int8),
        "int16" | "i16" => Ok(DataType::Int16),
        "int32" | "i32" => Ok(DataType::Int32),
        "int64" | "i64" => Ok(DataType::Int64),
        "uint8" | "u8" => Ok(DataType::UInt8),
        "uint16" | "u16" => Ok(DataType::UInt16),
        "uint32" | "u32" => Ok(DataType::UInt32),
        "uint64" | "u64" => Ok(DataType::UInt64),
        "float32" | "f32" => Ok(DataType::Float32),
        "float64" | "f64" => Ok(DataType::Float64),
        "bool" | "boolean" => Ok(DataType::Boolean),
        "string" | "str" | "utf8" => Ok(DataType::String),
        _ => Err(PolarsException::new(format!(
            "Unknown data type: {}. Supported: int8, int16, int32, int64, uint8, uint16, uint32, uint64, float32, float64, bool, string",
            dtype
        ))),
    }
}

/// Extract Vec<Expr> from a PHP ZendHashTable containing PolarsExpr objects
pub fn extract_exprs(expressions: &ZendHashTable) -> ExtResult<Vec<Expr>> {
    let mut exprs: Vec<Expr> = Vec::new();
    for (_, value) in expressions.iter() {
        let expr: &PolarsExpr = match value.extract::<&PolarsExpr>() {
            Some(expr) => expr,
            None => {
                return Err(PolarsException::new(
                    "Argument must be a list of \\Polars\\Expr objects".to_string(),
                ));
            }
        };
        exprs.push(expr.get_expr().clone());
    }
    Ok(exprs)
}

/// Convert a Polars AnyValue to a PHP Zval
pub fn any_value_to_zval(value: AnyValue) -> ExtResult<Zval> {
    let mut zval = Zval::new();
    match value {
        AnyValue::Null => Ok(zval),
        AnyValue::Boolean(b) => {
            zval.set_bool(b);
            Ok(zval)
        }
        AnyValue::Int8(i) => {
            zval.set_long(i as i64);
            Ok(zval)
        }
        AnyValue::Int16(i) => {
            zval.set_long(i as i64);
            Ok(zval)
        }
        AnyValue::Int32(i) => {
            zval.set_long(i as i64);
            Ok(zval)
        }
        AnyValue::Int64(i) => {
            zval.set_long(i);
            Ok(zval)
        }
        AnyValue::UInt8(u) => {
            zval.set_long(u as i64);
            Ok(zval)
        }
        AnyValue::UInt16(u) => {
            zval.set_long(u as i64);
            Ok(zval)
        }
        AnyValue::UInt32(u) => {
            zval.set_long(u as i64);
            Ok(zval)
        }
        AnyValue::UInt64(u) => {
            zval.set_long(u as i64);
            Ok(zval)
        }
        AnyValue::Float32(f) => {
            zval.set_double(f as f64);
            Ok(zval)
        }
        AnyValue::Float64(f) => {
            zval.set_double(f);
            Ok(zval)
        }
        AnyValue::String(s) => {
            zval.set_string(s, false).map_err(|e| {
                PolarsException::new(format!("Failed to set string: {}", e))
            })?;
            Ok(zval)
        }
        AnyValue::StringOwned(s) => {
            zval.set_string(&s.to_string(), false).map_err(|e| {
                PolarsException::new(format!("Failed to set string: {}", e))
            })?;
            Ok(zval)
        }
        _ => {
            zval.set_string(&format!("{}", value), false).map_err(|e| {
                PolarsException::new(format!("Failed to set string: {}", e))
            })?;
            Ok(zval)
        }
    }
}
