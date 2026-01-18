use ext_php_rs::types::Zval;
use polars::prelude::AnyValue;
use crate::exception::{ExtResult, PolarsException};

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
