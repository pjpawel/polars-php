use crate::common::any_value_to_zval;
use crate::data_type::PolarsDataType;
use crate::exception::{ExtResult, PolarsException};
use ext_php_rs::flags::DataType as PhpDataType;
use ext_php_rs::prelude::*;
use ext_php_rs::types::{ZendHashTable, Zval};
use ext_php_rs::zend::ce;
use polars::prelude::{
    ChunkCompareEq, ChunkCompareIneq, FillNullStrategy, IntoSeries, NamedFrom, Series,
    SortOptions,
};

/// Convert PHP array values to a Series
fn zval_vec_to_series(name: &str, values: Vec<Zval>) -> ExtResult<Series> {
    let first_value: &Zval = match values.first() {
        Some(value) => value,
        None => {
            let col_values: Vec<Option<String>> = vec![None; 0];
            return Ok(Series::new(name.into(), col_values));
        }
    };

    match first_value.get_type() {
        PhpDataType::Bool | PhpDataType::True | PhpDataType::False => {
            let col_values: Vec<Option<bool>> = values.iter().map(|v| v.bool()).collect();
            Ok(Series::new(name.into(), col_values))
        }
        PhpDataType::Long => {
            let col_values: Vec<Option<i64>> = values
                .iter()
                .map(|v| v.long().map(|x| x as i64))
                .collect();
            Ok(Series::new(name.into(), col_values))
        }
        PhpDataType::Double => {
            let col_values: Vec<Option<f64>> = values
                .iter()
                .map(|v| v.double().map(|x| x as f64))
                .collect();
            Ok(Series::new(name.into(), col_values))
        }
        PhpDataType::String => {
            let col_values: Vec<Option<String>> = values.iter().map(|v| v.string()).collect();
            Ok(Series::new(name.into(), col_values))
        }
        PhpDataType::Null => {
            let col_values: Vec<Option<String>> = vec![None; values.len()];
            Ok(Series::new(name.into(), col_values))
        }
        _ => Err(PolarsException::new(format!(
            "Unsupported type '{}' for Series",
            first_value.get_type()
        ))),
    }
}

#[php_class]
#[php(name = "Polars\\Series")]
#[php(implements(ce = ce::arrayaccess, stub = "\\ArrayAccess"))]
#[php(implements(ce = ce::countable, stub = "\\Countable"))]
#[derive(Clone)]
pub struct PhpSeries {
    pub inner: Series,
}

#[php_impl]
#[php(change_method_case = "camelCase")]
impl PhpSeries {
    // ==================== Constructor ====================

    /// Create a new Series from a PHP array
    ///
    /// # Example (PHP)
    /// ```php
    /// $s = new Series('values', [1, 2, 3, 4, 5]);
    /// $s = new Series('names', ['Alice', 'Bob', 'Charlie']);
    /// ```
    #[php(defaults(name = "".to_string()))]
    pub fn __construct(name: String, values: &ZendHashTable) -> ExtResult<Self> {
        let vals: Vec<Zval> = values.values().map(|v: &Zval| v.shallow_clone()).collect();
        let series = zval_vec_to_series(&name, vals)?;
        Ok(Self { inner: series })
    }

    // ==================== ArrayAccess Implementation ====================

    /// Check if an index exists
    #[php(name = "offsetExists")]
    pub fn offset_exists(&self, offset: &Zval) -> bool {
        if let Some(idx) = offset.long() {
            let idx = if idx < 0 {
                (self.inner.len() as i64 + idx) as usize
            } else {
                idx as usize
            };
            return idx < self.inner.len();
        }
        false
    }

    /// Get value at index
    #[php(name = "offsetGet")]
    pub fn offset_get(&self, offset: &Zval) -> ExtResult<Zval> {
        if let Some(idx) = offset.long() {
            let idx = if idx < 0 {
                (self.inner.len() as i64 + idx) as usize
            } else {
                idx as usize
            };
            if idx >= self.inner.len() {
                return Err(PolarsException::new(format!(
                    "Index {} out of bounds for Series with {} elements",
                    idx,
                    self.inner.len()
                )));
            }
            let value = self.inner.get(idx).map_err(|e| {
                PolarsException::new(format!("Failed to get value at index {}: {}", idx, e))
            })?;
            return any_value_to_zval(value);
        }
        Err(PolarsException::new(
            "Series index must be an integer".to_string(),
        ))
    }

    /// Set value at index - not supported
    /// @return void
    #[php(name = "offsetSet")]
    pub fn offset_set(&mut self, _offset: &Zval, _value: &Zval) -> ExtResult<()> {
        Err(PolarsException::new(
            "Series does not support item assignment".to_string(),
        ))
    }

    /// Unset value at index - not supported
    /// @return void
    #[php(name = "offsetUnset")]
    pub fn offset_unset(&mut self, _offset: &Zval) -> ExtResult<()> {
        Err(PolarsException::new(
            "Series does not support unsetting values".to_string(),
        ))
    }

    // ==================== Countable Implementation ====================

    /// Get the number of elements (Countable interface)
    #[php(name = "count")]
    pub fn php_count(&self) -> i64 {
        self.inner.len() as i64
    }

    // ==================== Attributes ====================

    /// Get the name of the Series
    #[php(getter)]
    pub fn get_name(&self) -> String {
        self.inner.name().to_string()
    }

    /// Get the data type of the Series
    #[php(getter)]
    pub fn get_dtype(&self) -> PolarsDataType {
        self.inner.dtype().clone().into()
    }

    /// Get the shape of the Series as [length]
    #[php(getter)]
    pub fn get_shape(&self) -> Vec<usize> {
        vec![self.inner.len()]
    }

    /// Get the number of elements in the Series
    pub fn len(&self) -> usize {
        self.inner.len()
    }

    /// Check if Series is empty
    #[php(name = "isEmpty")]
    pub fn is_empty(&self) -> bool {
        self.inner.is_empty()
    }

    // ==================== Element Access ====================

    /// Get the first n elements
    #[php(defaults(n = 10))]
    pub fn head(&self, n: i64) -> Self {
        Self {
            inner: self.inner.head(Some(n as usize)),
        }
    }

    /// Get the last n elements
    #[php(defaults(n = 10))]
    pub fn tail(&self, n: i64) -> Self {
        Self {
            inner: self.inner.tail(Some(n as usize)),
        }
    }

    /// Get a single value from the Series (must have exactly one element)
    pub fn item(&self) -> ExtResult<Zval> {
        if self.inner.len() != 1 {
            return Err(PolarsException::new(format!(
                "Series must have exactly one element to call item(). Got {} elements",
                self.inner.len()
            )));
        }
        let value = self
            .inner
            .get(0)
            .map_err(|e| PolarsException::new(format!("Failed to get value: {}", e)))?;
        any_value_to_zval(value)
    }

    /// Get the first element
    pub fn first(&self) -> ExtResult<Zval> {
        if self.inner.is_empty() {
            return Err(PolarsException::new("Series is empty".to_string()));
        }
        let value = self
            .inner
            .get(0)
            .map_err(|e| PolarsException::new(format!("Failed to get first value: {}", e)))?;
        any_value_to_zval(value)
    }

    /// Get the last element
    pub fn last(&self) -> ExtResult<Zval> {
        if self.inner.is_empty() {
            return Err(PolarsException::new("Series is empty".to_string()));
        }
        let idx = self.inner.len() - 1;
        let value = self
            .inner
            .get(idx)
            .map_err(|e| PolarsException::new(format!("Failed to get last value: {}", e)))?;
        any_value_to_zval(value)
    }

    /// Extract a slice of the Series
    pub fn slice(&self, offset: i64, length: i64) -> Self {
        Self {
            inner: self.inner.slice(offset, length as usize),
        }
    }

    // ==================== Aggregation Methods ====================

    /// Get the sum of all values
    pub fn sum(&self) -> ExtResult<Zval> {
        let result = self.inner.sum_reduce().map_err(|e| {
            PolarsException::new(format!("Cannot compute sum: {}", e))
        })?;
        any_value_to_zval(result.value().clone())
    }

    /// Get the mean of all values
    pub fn mean(&self) -> ExtResult<f64> {
        self.inner
            .mean()
            .ok_or_else(|| PolarsException::new("Cannot compute mean for this type".to_string()))
    }

    /// Get the median of all values
    pub fn median(&self) -> ExtResult<f64> {
        self.inner
            .median()
            .ok_or_else(|| PolarsException::new("Cannot compute median for this type".to_string()))
    }

    /// Get the minimum value
    pub fn min(&self) -> ExtResult<Zval> {
        let result = self.inner.min_reduce().map_err(|e| {
            PolarsException::new(format!("Cannot compute min: {}", e))
        })?;
        any_value_to_zval(result.value().clone())
    }

    /// Get the maximum value
    pub fn max(&self) -> ExtResult<Zval> {
        let result = self.inner.max_reduce().map_err(|e| {
            PolarsException::new(format!("Cannot compute max: {}", e))
        })?;
        any_value_to_zval(result.value().clone())
    }

    /// Get the standard deviation
    #[php(defaults(ddof = 1))]
    pub fn std(&self, ddof: u8) -> ExtResult<f64> {
        self.inner
            .std(ddof)
            .ok_or_else(|| PolarsException::new("Cannot compute std for this type".to_string()))
    }

    /// Get the variance
    #[php(name = "variance", defaults(ddof = 1))]
    pub fn variance(&self, ddof: u8) -> ExtResult<f64> {
        self.inner
            .var(ddof)
            .ok_or_else(|| PolarsException::new("Cannot compute variance for this type".to_string()))
    }

    /// Get the product of all values
    pub fn product(&self) -> ExtResult<Zval> {
        let result = self.inner.product().map_err(|e| {
            PolarsException::new(format!("Cannot compute product: {}", e))
        })?;
        any_value_to_zval(result.value().clone())
    }

    /// Count non-null values
    #[php(name = "countNonNull")]
    pub fn count_non_null(&self) -> usize {
        self.inner.len() - self.inner.null_count()
    }

    /// Count null values
    #[php(name = "nullCount")]
    pub fn null_count(&self) -> usize {
        self.inner.null_count()
    }

    /// Count unique values
    #[php(name = "nUnique")]
    pub fn n_unique(&self) -> ExtResult<usize> {
        self.inner
            .n_unique()
            .map_err(|e| PolarsException::new(format!("Cannot count unique values: {}", e)))
    }

    // ==================== Boolean Operations ====================

    /// Check which values are null
    #[php(name = "isNull")]
    pub fn is_null(&self) -> Self {
        Self {
            inner: self.inner.is_null().into_series(),
        }
    }

    /// Check which values are not null
    #[php(name = "isNotNull")]
    pub fn is_not_null(&self) -> Self {
        Self {
            inner: self.inner.is_not_null().into_series(),
        }
    }

    /// Check which values are NaN
    #[php(name = "isNan")]
    pub fn is_nan(&self) -> ExtResult<Self> {
        let result = self.inner.is_nan().map_err(|e| {
            PolarsException::new(format!("Cannot check NaN: {}", e))
        })?;
        Ok(Self {
            inner: result.into_series(),
        })
    }

    /// Check which values are not NaN
    #[php(name = "isNotNan")]
    pub fn is_not_nan(&self) -> ExtResult<Self> {
        let result = self.inner.is_not_nan().map_err(|e| {
            PolarsException::new(format!("Cannot check not NaN: {}", e))
        })?;
        Ok(Self {
            inner: result.into_series(),
        })
    }

    /// Check if any value is true (for boolean Series)
    pub fn any(&self) -> ExtResult<bool> {
        let ca = self.inner.bool().map_err(|e| {
            PolarsException::new(format!("Series must be boolean type for any(): {}", e))
        })?;
        Ok(ca.any())
    }

    /// Check if all values are true (for boolean Series)
    pub fn all(&self) -> ExtResult<bool> {
        let ca = self.inner.bool().map_err(|e| {
            PolarsException::new(format!("Series must be boolean type for all(): {}", e))
        })?;
        Ok(ca.all())
    }

    // ==================== Comparison Operations ====================

    /// Element-wise equality comparison
    /// @param int|float|string|bool|null $other
    pub fn eq(&self, other: &Zval) -> ExtResult<Self> {
        let other_series = zval_to_scalar_series(&self.inner, other)?;
        let result = self.inner.equal(&other_series).map_err(|e| {
            PolarsException::new(format!("Comparison failed: {}", e))
        })?;
        Ok(Self {
            inner: result.into_series(),
        })
    }

    /// Element-wise inequality comparison
    /// @param int|float|string|bool|null $other
    pub fn ne(&self, other: &Zval) -> ExtResult<Self> {
        let other_series = zval_to_scalar_series(&self.inner, other)?;
        let result = self.inner.not_equal(&other_series).map_err(|e| {
            PolarsException::new(format!("Comparison failed: {}", e))
        })?;
        Ok(Self {
            inner: result.into_series(),
        })
    }

    /// Element-wise less than comparison
    /// @param int|float|string|bool|null $other
    pub fn lt(&self, other: &Zval) -> ExtResult<Self> {
        let other_series = zval_to_scalar_series(&self.inner, other)?;
        let result = self.inner.lt(&other_series).map_err(|e| {
            PolarsException::new(format!("Comparison failed: {}", e))
        })?;
        Ok(Self {
            inner: result.into_series(),
        })
    }

    /// Element-wise less than or equal comparison
    /// @param int|float|string|bool|null $other
    pub fn le(&self, other: &Zval) -> ExtResult<Self> {
        let other_series = zval_to_scalar_series(&self.inner, other)?;
        let result = self.inner.lt_eq(&other_series).map_err(|e| {
            PolarsException::new(format!("Comparison failed: {}", e))
        })?;
        Ok(Self {
            inner: result.into_series(),
        })
    }

    /// Element-wise greater than comparison
    /// @param int|float|string|bool|null $other
    pub fn gt(&self, other: &Zval) -> ExtResult<Self> {
        let other_series = zval_to_scalar_series(&self.inner, other)?;
        let result = self.inner.gt(&other_series).map_err(|e| {
            PolarsException::new(format!("Comparison failed: {}", e))
        })?;
        Ok(Self {
            inner: result.into_series(),
        })
    }

    /// Element-wise greater than or equal comparison
    /// @param int|float|string|bool|null $other
    pub fn ge(&self, other: &Zval) -> ExtResult<Self> {
        let other_series = zval_to_scalar_series(&self.inner, other)?;
        let result = self.inner.gt_eq(&other_series).map_err(|e| {
            PolarsException::new(format!("Comparison failed: {}", e))
        })?;
        Ok(Self {
            inner: result.into_series(),
        })
    }

    // ==================== Data Manipulation ====================

    /// Sort the Series
    #[php(defaults(descending = false, nullsLast = true))]
    #[allow(non_snake_case)]
    pub fn sort(&self, descending: bool, nullsLast: bool) -> ExtResult<Self> {
        let options = SortOptions::default()
            .with_order_descending(descending)
            .with_nulls_last(nullsLast);
        let sorted = self.inner.sort(options).map_err(|e| {
            PolarsException::new(format!("Sort failed: {}", e))
        })?;
        Ok(Self { inner: sorted })
    }

    /// Reverse the Series
    pub fn reverse(&self) -> Self {
        Self {
            inner: self.inner.reverse(),
        }
    }

    /// Get unique values
    pub fn unique(&self) -> ExtResult<Self> {
        let unique = self.inner.unique().map_err(|e| {
            PolarsException::new(format!("Failed to get unique values: {}", e))
        })?;
        Ok(Self { inner: unique })
    }

    /// Remove null values
    #[php(name = "dropNulls")]
    pub fn drop_nulls(&self) -> Self {
        Self {
            inner: self.inner.drop_nulls(),
        }
    }

    /// Fill null values using forward strategy
    #[php(name = "fillNullForward")]
    pub fn fill_null_forward(&self) -> ExtResult<Self> {
        let filled = self
            .inner
            .fill_null(FillNullStrategy::Forward(None))
            .map_err(|e| PolarsException::new(format!("Failed to fill null values: {}", e)))?;
        Ok(Self { inner: filled })
    }

    /// Fill null values using backward strategy
    #[php(name = "fillNullBackward")]
    pub fn fill_null_backward(&self) -> ExtResult<Self> {
        let filled = self
            .inner
            .fill_null(FillNullStrategy::Backward(None))
            .map_err(|e| PolarsException::new(format!("Failed to fill null values: {}", e)))?;
        Ok(Self { inner: filled })
    }

    /// Fill null values with the mean
    #[php(name = "fillNullMean")]
    pub fn fill_null_mean(&self) -> ExtResult<Self> {
        let filled = self
            .inner
            .fill_null(FillNullStrategy::Mean)
            .map_err(|e| PolarsException::new(format!("Failed to fill null values: {}", e)))?;
        Ok(Self { inner: filled })
    }

    /// Fill null values with zero
    #[php(name = "fillNullZero")]
    pub fn fill_null_zero(&self) -> ExtResult<Self> {
        let filled = self
            .inner
            .fill_null(FillNullStrategy::Zero)
            .map_err(|e| PolarsException::new(format!("Failed to fill null values: {}", e)))?;
        Ok(Self { inner: filled })
    }

    // ==================== Utility Methods ====================

    /// Convert Series to PHP array
    #[php(name = "toArray")]
    pub fn to_array(&self) -> ExtResult<Vec<Zval>> {
        let mut result = Vec::with_capacity(self.inner.len());
        for idx in 0..self.inner.len() {
            let value = self.inner.get(idx).map_err(|e| {
                PolarsException::new(format!("Failed to get value at index {}: {}", idx, e))
            })?;
            result.push(any_value_to_zval(value)?);
        }
        Ok(result)
    }

    /// Rename the Series
    pub fn rename(&self, name: String) -> Self {
        let mut s = self.inner.clone();
        s.rename(name.into());
        Self { inner: s }
    }

    /// Create an alias for the Series (same as rename)
    pub fn alias(&self, name: String) -> Self {
        self.rename(name)
    }

    /// Create a copy of the Series
    pub fn copy(&self) -> Self {
        Self {
            inner: self.inner.clone(),
        }
    }

    /// Cast Series to a different data type
    /// @param string $dtype One of: 'int8', 'int16', 'int32', 'int64', 'uint8', 'uint16', 'uint32', 'uint64', 'float32', 'float64', 'bool', 'string'
    pub fn cast(&self, dtype: String) -> ExtResult<Self> {
        let target_type = crate::common::parse_dtype(&dtype)?;
        let casted = self.inner.cast(&target_type).map_err(|e| {
            PolarsException::new(format!("Cast to {} failed: {}", dtype, e))
        })?;
        Ok(Self { inner: casted })
    }

    /// Display the Series
    #[php(name = "__toString")]
    pub fn __to_string(&self) -> String {
        format!("{}", self.inner)
    }
}

// ==================== Helper Functions ====================

/// Convert a PHP Zval to a Series with a single value, matching the dtype of the reference series
fn zval_to_scalar_series(reference: &Series, value: &Zval) -> ExtResult<Series> {
    let len = reference.len();
    let name = reference.name();

    match value.get_type() {
        PhpDataType::Long => {
            let v = value.long().unwrap() as i64;
            Ok(Series::new(name.clone(), vec![v; len]))
        }
        PhpDataType::Double => {
            let v = value.double().unwrap();
            Ok(Series::new(name.clone(), vec![v; len]))
        }
        PhpDataType::String => {
            let v = value.string().unwrap();
            Ok(Series::new(name.clone(), vec![v; len]))
        }
        PhpDataType::Bool | PhpDataType::True | PhpDataType::False => {
            let v = value.bool().unwrap();
            Ok(Series::new(name.clone(), vec![v; len]))
        }
        PhpDataType::Null => {
            let values: Vec<Option<i64>> = vec![None; len];
            Ok(Series::new(name.clone(), values))
        }
        _ => Err(PolarsException::new(format!(
            "Unsupported type for comparison: {}",
            value.get_type()
        ))),
    }
}

// ==================== Trait Implementations ====================

impl From<Series> for PhpSeries {
    fn from(series: Series) -> Self {
        PhpSeries { inner: series }
    }
}

impl From<PhpSeries> for Series {
    fn from(php_series: PhpSeries) -> Self {
        php_series.inner
    }
}
