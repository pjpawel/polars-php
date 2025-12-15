use ext_php_rs::prelude::*;
use ext_php_rs::zend::ce;
use polars::prelude::PolarsError;

#[php_class]
#[php(name = "Polars\\Exception")]
#[php(extends(ce = ce::exception, stub = "\\Exception"))]
#[derive(Debug)]
pub struct PolarsException(String);

impl PolarsException {

    pub fn new(msg: String) -> Self {
        Self(msg)
    }

    pub fn as_str(&self) -> &str {
        self.0.as_str()
    }

}


impl Into<PolarsException> for PolarsError {
    fn into(self) -> PolarsException {
        PolarsException(format!("Exception during processing: {}", self.to_string()))
    }
}

impl Into<PhpException> for PolarsException {
    fn into(self) -> PhpException {
        PhpException::default(self.0)
    }
}

/// Represent this extension result - based on PHPExtension
pub type ExtResult<T = ()> = Result<T, PolarsException>;