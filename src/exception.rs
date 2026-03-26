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

impl From<PolarsError> for PolarsException {
    fn from(err: PolarsError) -> Self {
        PolarsException(format!("Exception during processing: {}", err))
    }
}

impl From<PolarsException> for PhpException {
    fn from(polars_exception: PolarsException) -> PhpException {
        PhpException::default(polars_exception.0)
    }
}

/// Represent this extension result - based on PHPExtension
pub type ExtResult<T = ()> = Result<T, PolarsException>;
