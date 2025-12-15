// use ext_php_rs::flags::DataType;
// use ext_php_rs::prelude::PhpResult;
// use ext_php_rs::types::Zval;
// use polars::prelude::LiteralValue;
//
// pub(crate) fn php_to_literal(value: Zval) -> PhpResult<LiteralValue> {
//     match value.get_type() {
//         DataType::Long => Ok(LiteralValue::Int64(value.long()?)),
//         DataType::Double => Ok(LiteralValue::Float64(value.double()?)),
//         DataType::String => Ok(LiteralValue::String(
//             value.str()?.to_string().into()
//         )),
//         DataType::True => Ok(LiteralValue::Boolean(true)),
//         DataType::False => Ok(LiteralValue::Boolean(false)),
//         DataType::Null => Ok(LiteralValue::Null),
//         _ => Err("Unsupported type".into()),
//     }
// }