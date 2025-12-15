use ext_php_rs::prelude::*;
use polars::prelude::DataType;


#[php_class]
#[php(name = "Polars\\DataType")]
#[derive(Clone, Debug, PartialEq)]
pub struct PolarsDataType(DataType);

impl PolarsDataType {

    // #[php(name = "__toString")]
    // pub fn __to_string(&self) -> String { self.0.to_string() }

}

impl From<DataType> for PolarsDataType {
    fn from(dtype: DataType) -> Self {
        PolarsDataType(dtype)
    }
}