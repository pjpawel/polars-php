use crate::common::{any_value_to_zval, extract_exprs, parse_dtype};
use crate::data_type::PolarsDataType;
use crate::exception::{ExtResult, PolarsException};
use crate::expression::PolarsExpr;
use crate::lazy_group_by::PhpLazyGroupBy;
use crate::series::PhpSeries;
use ext_php_rs::flags::DataType as PhpDataType;
use ext_php_rs::prelude::*;
use ext_php_rs::types::{ArrayKey, ZendHashTable, Zval};
use ext_php_rs::zend::ce;
use polars::lazy::dsl::{all, col, lit, Expr};
use polars::prelude::{
    Column, CsvParseOptions, CsvReadOptions, CsvWriter, DataFrame, IntoLazy, IntoSeries,
    JoinArgs, JoinType, JsonFormat, JsonReader, JsonWriter, OptFlags, ParquetWriter, PlSmallStr,
    QuantileMethod, Selector, SerReader, SerWriter, SortMultipleOptions, UniqueKeepStrategy,
};
use std::collections::HashMap;

fn parse_array_to_cols_by_keys(data: &ZendHashTable) -> ExtResult<Vec<Column>> {
    let mut columns = Vec::new();
    for (key, value) in data.iter() {
        let col_name = match key {
            ArrayKey::String(s) => s,
            ArrayKey::Str(s) => s.to_string(),
            ArrayKey::Long(i) => i.to_string(),
        };

        let arr: &ZendHashTable = match value.array() {
            Some(a) => a,
            None => {
                return Err(PolarsException::new(format!(
                    "Column '{}' must be an array",
                    col_name
                )));
            }
        };

        let col_vals = arr
            .values()
            .into_iter()
            .map(|zv| zv.shallow_clone())
            .collect();

        let col = col_vals_to_column(&col_name, col_vals)?;
        columns.push(col);
    }
    Ok(columns)
}

fn col_vals_to_column(name: &str, values: Vec<Zval>) -> ExtResult<Column> {
    let first_value: &Zval = match values.first() {
        Some(value) => value,
        None => {
            let col_values: Vec<Option<String>> = vec![None; 0];
            return Ok(Column::new(name.into(), col_values));
        }
    };
    match first_value.get_type() {
        PhpDataType::Bool => {
            let col_values: Vec<Option<bool>> = values
                .iter()
                .map(|v: &Zval| v.bool()) // returns Option<bool>
                .collect();
            Ok(Column::new(name.into(), col_values))
        }
        PhpDataType::Long => {
            let col_values: Vec<Option<i64>> = values
                .iter()
                .map(|v: &Zval| match v.long() {
                    Some(v) => Some(v as i64),
                    None => None,
                })
                .collect();
            Ok(Column::new(name.into(), col_values))
        }
        PhpDataType::Double => {
            let col_values: Vec<Option<f64>> = values
                .iter()
                .map(|v: &Zval| match v.double() {
                    Some(v) => Some(v as f64),
                    None => None,
                })
                .collect();
            Ok(Column::new(name.into(), col_values))
        }
        PhpDataType::String => {
            let col_values: Vec<Option<String>> = values.iter().map(|v| v.string()).collect();
            Ok(Column::new(name.into(), col_values))
        }
        PhpDataType::Null => {
            let col_values: Vec<Option<String>> = vec![None; values.len()];
            Ok(Column::new(name.into(), col_values))
        }
        default => Err(PolarsException::new(format!(
            "Unsupported type '{}' for column '{}'",
            first_value.get_type(),
            name
        ))),
    }
}

#[php_class]
#[php(name = "Polars\\DataFrame")]
#[php(implements(ce = ce::arrayaccess, stub = "\\ArrayAccess"))]
#[derive(Clone)]
pub struct PhpDataFrame {
    pub inner: DataFrame,
}

#[php_impl]
#[php(change_method_case = "camelCase")]
impl PhpDataFrame {

    /// Create a new DataFrame from a PHP array
    /// keys are column name
    ///
    /// # Example (PHP)
    /// ```php
    /// $df = new DataFrame([
    ///     'name' => ['Alice', 'Bob', 'Charlie'],
    ///     'age' => [25, 30, 35],
    ///     'city' => ['NYC', 'LA', 'Chicago']
    /// ]);
    /// ```
    #[php(defaults(byKeys = true))]
    pub fn __construct(
        data: &ZendHashTable,
        #[allow(non_snake_case)] byKeys: bool,
    ) -> ExtResult<Self> {
        let col_vec = match data.is_empty() {
            true => Vec::new(),
            false => match byKeys {
                true => parse_array_to_cols_by_keys(data)?,
                false => {
                    return Err(PolarsException::new(
                        "Parsing data by first row header is currently not implemented".to_string(),
                    ));
                }
            },
        };
        let df = DataFrame::new(col_vec)
            .map_err(|e| PolarsException::new(format!("Failed to create DataFrame: {}", e)))?;
        Ok(Self { inner: df })
    }

    // Lazy //

    /// Convert this DataFrame to a LazyFrame for lazy evaluation
    /// @return \Polars\LazyFrame
    pub fn lazy(&self) -> crate::lazy_frame::PhpLazyFrame {
        crate::lazy_frame::PhpLazyFrame {
            inner: self.inner.clone().lazy(),
        }
    }

    // Static methods //

    // Array Access //

    /// Check if an offset (column name) exists
    #[php(name = "offsetExists")]
    pub fn offset_exists(&self, offset: &Zval) -> bool {
        // Method required by ArrayAccess interface
        if let Some(col_name) = offset.string() {
            return self.inner.get_column_names().iter().any(|c| c.as_str() == col_name);
        }
        if let Some(idx) = offset.long() {
            let idx = if idx < 0 {
                (self.inner.height() as i64 + idx) as usize
            } else {
                idx as usize
            };
            return idx < self.inner.height();
        }
        false
    }

    /// Get value at offset
    ///
    /// Supports:
    /// - String offset: returns single column as DataFrame $df['col1']
    /// - Integer offset: returns single row as DataFrame $df[1]
    /// - Array of strings: returns DataFrame with specified columns $df[['col1', 'col2']]
    /// - Array of string and integer: returns specific cells $df[['col1', 1]], $df[['col1', 'col2', 0]]
    ///
    /// @param $offset string|int|array
    #[php(name = "offsetGet")]
    pub fn offset_get(&self, offset: &Zval) -> ExtResult<Self> {
        // Method required by ArrayAccess interface
        // Single column by name
        if let Some(col_name) = offset.string() {
            let col = self.inner.column(&col_name).map_err(|e| {
                PolarsException::new(format!("Column '{}' not found: {}", col_name, e))
            })?;
            let df = DataFrame::new(vec![col.clone()]).map_err(|e| {
                PolarsException::new(format!("Failed to create DataFrame: {}", e))
            })?;
            return Ok(Self { inner: df });
        }

        // Single row by index
        if let Some(idx) = offset.long() {
            let idx = if idx < 0 {
                (self.inner.height() as i64 + idx) as usize
            } else {
                idx as usize
            };
            if idx >= self.inner.height() {
                return Err(PolarsException::new(format!(
                    "Row index {} out of bounds for DataFrame with {} rows",
                    idx,
                    self.inner.height()
                )));
            }
            let df = self.inner.slice(idx as i64, 1);
            return Ok(Self { inner: df });
        }

        // Multiple columns by array of names, optionally with row index
        // Supports: $df[['col1', 'col2']] or $df[['col1', 'col2', 0]]
        if let Some(arr) = offset.array() {
            let col_names: Vec<String> = arr
                .values()
                .filter_map(|v| v.string())
                .collect();

            // Look for an integer (row index) in the array
            let row_idx: Option<i64> = arr.values().find_map(|v| v.long());

            if col_names.is_empty() {
                return Err(PolarsException::new(
                    "Array offset must contain at least one column name as string".to_string(),
                ));
            }

            let cols: Result<Vec<Column>, _> = col_names
                .iter()
                .map(|name| {
                    self.inner
                        .column(name)
                        .map(|c| c.clone())
                        .map_err(|e| format!("Column '{}' not found: {}", name, e))
                })
                .collect();

            let cols = cols.map_err(|e| PolarsException::new(e))?;
            let mut df = DataFrame::new(cols).map_err(|e| {
                PolarsException::new(format!("Failed to create DataFrame: {}", e))
            })?;

            // If row index is provided, slice to that row
            if let Some(idx) = row_idx {
                let idx = if idx < 0 {
                    (df.height() as i64 + idx) as usize
                } else {
                    idx as usize
                };
                if idx >= df.height() {
                    return Err(PolarsException::new(format!(
                        "Row index {} out of bounds for DataFrame with {} rows",
                        idx,
                        df.height()
                    )));
                }
                df = df.slice(idx as i64, 1);
            }

            return Ok(Self { inner: df });
        }

        Err(PolarsException::new(
            "Offset must be a string (column name), integer (row index), or array of column names (optionally with row index)".to_string(),
        ))
    }

    /// Set value at offset - not supported for DataFrames
    /// @return void
    #[php(name = "offsetSet")]
    pub fn offset_set(&mut self, _offset: &Zval, _value: &Zval) -> ExtResult<()> {
        // Method required by ArrayAccess interface
        Err(PolarsException::new(
            "DataFrame does not support item assignment. Use withColumn() or similar methods instead.".to_string(),
        ))
    }

    /// Unset value at offset - not supported for DataFrames
    /// @return void
    #[php(name = "offsetUnset")]
    pub fn offset_unset(&mut self, _offset: &Zval) -> ExtResult<()> {
        // Method required by ArrayAccess interface
        Err(PolarsException::new(
            "DataFrame does not support unsetting columns. Use drop() method instead.".to_string(),
        ))
    }

    // Attributes //

    /// Get columns names
    /// @returns string[]
    #[php(getter)]
    pub fn get_columns(&self) -> Vec<String> {
        self.inner
            .get_column_names()
            .iter()
            .map(|c| c.to_string())
            .collect()
    }

    /// Set columns names
    /// @param string[] $columns - length of list must be equal to current length of columns
    /// @return void
    #[php(setter)]
    pub fn set_columns(&mut self, columns: Vec<String>) {
        let str_refs: Vec<&str> = columns.iter().map(|s| s.as_str()).collect();
        if let Err(e) = self.inner.set_column_names(str_refs) {
            ext_php_rs::exception::PhpException::default(format!(
                "Failed to set DataFrame column names: {}",
                e
            ))
            .throw()
            .ok();
        }
    }

    /// @return \Polars\DataType[]
    #[php(getter)]
    pub fn dtypes(&self) -> Vec<PolarsDataType> {
        self.inner
            .dtypes()
            .iter()
            .map(|d| d.clone().into())
            .collect()
    }

    /// @return int Get the number of rows
    pub fn height(&self) -> usize {
        self.inner.height()
    }

    /// @return int[] Get the shape of the DataFrame as [rows, columns]
    pub fn shape(&self) -> Vec<usize> {
        let shape = self.inner.shape();
        vec![shape.0, shape.1]
    }

    /// @return int Get the number of columns
    pub fn width(&self) -> usize {
        self.inner.width()
    }

    // Aggregation //

    /// @return \Polars\DataFrame Return the number of non-null elements for each column.
    pub fn count(&self) -> ExtResult<Self> {
        let inner = match self
            .inner
            .clone()
            .lazy()
            .count()
            .with_optimizations(OptFlags::EAGER)
            .collect()
        {
            Ok(v) => v,
            Err(e) => {
                return Err(PolarsException::new(format!(
                    "Cannot execute count operation: {}",
                    e.to_string()
                )));
            }
        };
        Ok(Self { inner })
    }

    /// Aggregate the columns of this DataFrame to their maximum value.
    pub fn max(&self) -> ExtResult<Self> {
        let inner = match self
            .inner
            .clone()
            .lazy()
            .max()
            .with_optimizations(OptFlags::EAGER)
            .collect()
        {
            Ok(v) => v,
            Err(e) => {
                return Err(PolarsException::new(format!(
                    "Cannot execute max operation: {}",
                    e.to_string()
                )));
            }
        };
        Ok(Self { inner })
    }

    // /// Get the maximum value horizontally across columns.
    // pub fn max_horizontal(&self) -> ExtResult<Self> {
    //     //use select https://github.com/pola-rs/polars/blob/py-1.35.2/py-polars/src/polars/dataframe/frame.py#L10422
    //     todo!()
    // }

    /// Aggregate the columns of this DataFrame to their mean value.
    pub fn mean(&self) -> ExtResult<Self> {
        let inner = match self
            .inner
            .clone()
            .lazy()
            .mean()
            .with_optimizations(OptFlags::EAGER)
            .collect()
        {
            Ok(v) => v,
            Err(e) => {
                return Err(PolarsException::new(format!(
                    "Cannot execute min operation: {}",
                    e.to_string()
                )));
            }
        };
        Ok(Self { inner })
    }

    /// Aggregate the columns of this DataFrame to their minimal value.
    pub fn min(&self) -> ExtResult<Self> {
        let inner = match self
            .inner
            .clone()
            .lazy()
            .min()
            .with_optimizations(OptFlags::EAGER)
            .collect()
        {
            Ok(v) => v,
            Err(e) => {
                return Err(PolarsException::new(format!(
                    "Cannot execute min operation: {}",
                    e.to_string()
                )));
            }
        };
        Ok(Self { inner })
    }

    /// Aggregate the columns of this DataFrame to their product value.
    #[php(defaults(ddof = 0))]
    pub fn std(&self, ddof: u8) -> ExtResult<Self> {
        let inner = match self
            .inner
            .clone()
            .lazy()
            .std(ddof) //https://numpy.org/doc/stable/reference/generated/numpy.std.html#
            .with_optimizations(OptFlags::EAGER)
            .collect()
        {
            Ok(v) => v,
            Err(e) => {
                return Err(PolarsException::new(format!(
                    "Cannot execute min operation: {}",
                    e.to_string()
                )));
            }
        };
        Ok(Self { inner })
    }

    /// Make select based on given expressions
    /// @param \Polars\Expr[]
    pub fn select(&self, expressions: &ZendHashTable) -> ExtResult<Self> {
        let exprs = crate::common::extract_exprs(expressions)?;
        self._do_select(exprs)
    }

    // Rows //

    /// Get the first n rows
    #[php(defaults(n = 10))]
    pub fn head(&self, n: i32) -> Self {
        Self {
            inner: self.inner.head(Some(n as usize)),
        }
    }

    /// Get the last n rows
    #[php(defaults(n = 10))]
    pub fn tail(&self, n: i32) -> Self {
        Self {
            inner: self.inner.tail(Some(n as usize)),
        }
    }

    // Miscellaneous //

    /// Return the DataFrame as a scalar value
    /// The DataFrame must contain exactly one element (1 row, 1 column)
    pub fn item(&self) -> ExtResult<Zval> {
        if self.inner.height() != 1 || self.inner.width() != 1 {
            return Err(PolarsException::new(format!(
                "DataFrame must have exactly one element to call item(). Got shape: ({}, {})",
                self.inner.height(),
                self.inner.width()
            )));
        }

        let col = self
            .inner
            .get_columns()
            .first()
            .ok_or_else(|| PolarsException::new("Failed to get column".to_string()))?;

        let value = col
            .get(0)
            .map_err(|e| PolarsException::new(format!("Failed to get value: {}", e)))?;

        any_value_to_zval(value)
    }

    /// Get a single column as a Series
    /// @return \Polars\Series
    pub fn column(&self, name: String) -> ExtResult<PhpSeries> {
        let col = self.inner.column(&name).map_err(|e| {
            PolarsException::new(format!("Column '{}' not found: {}", name, e))
        })?;
        Ok(PhpSeries::from(col.clone().take_materialized_series()))
    }

    /// Get all columns as an array of Series
    /// @return \Polars\Series[]
    #[php(name = "getSeries")]
    pub fn get_series(&self) -> Vec<PhpSeries> {
        self.inner
            .get_columns()
            .iter()
            .map(|c| PhpSeries::from(c.clone().take_materialized_series()))
            .collect()
    }

    /// Check if DataFrame is empty
    pub fn is_empty(&self) -> bool {
        self.inner.is_empty()
    }

    /// Create a copy of the DataFrame
    pub fn copy(&self) -> Self {
        Self {
            inner: self.inner.clone(),
        }
    }

    /// Display the DataFrame (returns a formatted string)
    #[php(name = "__toString")]
    pub fn __to_string(&self) -> String {
        format!("{}", self.inner)
    }

    /// Read a DataFrame from a CSV file
    #[allow(non_snake_case)]
    #[php(defaults(hasHeader = true, separator = ",".to_string()))]
    pub fn read_csv(
        path: String,
        hasHeader: bool,
        separator: String,
    ) -> ExtResult<Self> {
        if separator.len() != 1 {
            return Err(PolarsException::new("Separator must of length 1".to_string()));
        }
        let df = CsvReadOptions::default()
            .with_has_header(hasHeader)
            .with_parse_options(
                CsvParseOptions::default()
                    .with_try_parse_dates(true)
                    .with_separator(separator.as_bytes().first().unwrap().to_owned())
            )
            .try_into_reader_with_file_path(Some(path.into()))
            .map_err(|e| PolarsException::new(format!("Failed to read CSV: {}", e)))?
            .finish()
            .map_err(|e| PolarsException::new(format!("Failed to create df from CSV file: {}", e)))?;
        Ok(Self { inner: df })
    }

    /// Read a DataFrame from a JSON file
    pub fn read_json(path: String) -> ExtResult<Self> {
        let file = std::fs::File::open(&path)
            .map_err(|e| PolarsException::new(format!("Failed to open file: {}", e)))?;
        let df = JsonReader::new(file)
            .with_json_format(JsonFormat::Json)
            .finish()
            .map_err(|e| PolarsException::new(format!("Failed to read JSON: {}", e)))?;
        Ok(Self { inner: df })
    }

    /// Read a DataFrame from a NDJSON (newline-delimited JSON) file
    pub fn read_ndjson(path: String) -> ExtResult<Self> {
        let file = std::fs::File::open(&path)
            .map_err(|e| PolarsException::new(format!("Failed to open file: {}", e)))?;
        let df = JsonReader::new(file)
            .with_json_format(JsonFormat::JsonLines)
            .finish()
            .map_err(|e| PolarsException::new(format!("Failed to read NDJSON: {}", e)))?;
        Ok(Self { inner: df })
    }

    /// Read a DataFrame from a Parquet file
    pub fn read_parquet(path: String) -> ExtResult<Self> {
        let file = std::fs::File::open(&path)
            .map_err(|e| PolarsException::new(format!("Failed to open file: {}", e)))?;
        let df = polars::prelude::ParquetReader::new(file)
            .finish()
            .map_err(|e| PolarsException::new(format!("Failed to read Parquet: {}", e)))?;
        Ok(Self { inner: df })
    }

    /// Write to CSV file
    #[php(defaults(includeHeaders = true, separator = ','.to_string()))]
    pub fn write_csv(
        &self,
        path: String,
        #[allow(non_snake_case)] includeHeader: bool,
        separator: String,
    ) -> ExtResult<()> {
        if separator.len() != 1 {
            return Err(PolarsException::new("Separator must of length 1".to_string()));
        }

        let mut file = std::fs::File::create(&path)
            .map_err(|e| PolarsException::new(format!("Failed to create file: {}", e)))?;

        CsvWriter::new(&mut file)
            .include_header(includeHeader)
            .with_separator(*separator.as_bytes().first().unwrap())
            .finish(&mut self.inner.clone())
            .map_err(|e| PolarsException::new(format!("Failed to write CSV: {}", e)))?;

        Ok(())
    }

    /// Write DataFrame to a JSON file
    pub fn write_json(&self, path: String) -> ExtResult<()> {
        let mut file = std::fs::File::create(&path)
            .map_err(|e| PolarsException::new(format!("Failed to create file: {}", e)))?;

        JsonWriter::new(&mut file)
            .with_json_format(JsonFormat::Json)
            .finish(&mut self.inner.clone())
            .map_err(|e| PolarsException::new(format!("Failed to write JSON: {}", e)))?;

        Ok(())
    }

    /// Write DataFrame to a NDJSON (newline-delimited JSON) file
    pub fn write_ndjson(&self, path: String) -> ExtResult<()> {
        let mut file = std::fs::File::create(&path)
            .map_err(|e| PolarsException::new(format!("Failed to create file: {}", e)))?;

        JsonWriter::new(&mut file)
            .with_json_format(JsonFormat::JsonLines)
            .finish(&mut self.inner.clone())
            .map_err(|e| PolarsException::new(format!("Failed to write NDJSON: {}", e)))?;

        Ok(())
    }

    /// Write DataFrame to a Parquet file
    pub fn write_parquet(&self, path: String) -> ExtResult<()> {
        let file = std::fs::File::create(&path)
            .map_err(|e| PolarsException::new(format!("Failed to create file: {}", e)))?;

        ParquetWriter::new(file)
            .finish(&mut self.inner.clone())
            .map_err(|e| PolarsException::new(format!("Failed to write Parquet: {}", e)))?;

        Ok(())
    }

    // Phase 1: Core Manipulation //

    /// Sort DataFrame by a column
    #[allow(non_snake_case)]
    #[php(defaults(descending = false, nullsLast = true))]
    pub fn sort(&self, column: String, descending: bool, nullsLast: bool) -> ExtResult<Self> {
        let opts = SortMultipleOptions::new()
            .with_order_descending(descending)
            .with_nulls_last(nullsLast);
        let inner = self
            .inner
            .clone()
            .lazy()
            .sort([&column], opts)
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| PolarsException::new(format!("Sort failed: {}", e)))?;
        Ok(Self { inner })
    }

    /// Drop specified columns
    /// @param string[] $columns
    pub fn drop(&self, columns: Vec<String>) -> ExtResult<Self> {
        let names: Vec<PlSmallStr> = columns.into_iter().map(PlSmallStr::from).collect();
        let selector = Selector::ByName {
            names: names.into(),
            strict: false,
        };
        let inner = self
            .inner
            .clone()
            .lazy()
            .drop(selector)
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| PolarsException::new(format!("Drop failed: {}", e)))?;
        Ok(Self { inner })
    }

    /// Rename columns
    /// @param string[] $existing Old column names
    /// @param string[] $newNames New column names
    #[allow(non_snake_case)]
    pub fn rename(&self, existing: Vec<String>, newNames: Vec<String>) -> ExtResult<Self> {
        let inner = self
            .inner
            .clone()
            .lazy()
            .rename(existing, newNames, true)
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| PolarsException::new(format!("Rename failed: {}", e)))?;
        Ok(Self { inner })
    }

    /// Filter rows by expression
    /// @param \Polars\Expr $expression
    pub fn filter(&self, expression: &PolarsExpr) -> ExtResult<Self> {
        let inner = self
            .inner
            .clone()
            .lazy()
            .filter(expression.get_expr().clone())
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| PolarsException::new(format!("Filter failed: {}", e)))?;
        Ok(Self { inner })
    }

    /// Add or modify columns
    /// @param \Polars\Expr[] $expressions
    #[php(name = "withColumns")]
    pub fn with_columns(&self, expressions: &ZendHashTable) -> ExtResult<Self> {
        let exprs = extract_exprs(expressions)?;
        let inner = self
            .inner
            .clone()
            .lazy()
            .with_columns(&exprs)
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| PolarsException::new(format!("withColumns failed: {}", e)))?;
        Ok(Self { inner })
    }

    /// Group by expressions
    /// @param \Polars\Expr[] $expressions
    /// @return \Polars\LazyGroupBy
    #[php(name = "groupBy")]
    pub fn group_by(&self, expressions: &ZendHashTable) -> ExtResult<PhpLazyGroupBy> {
        let exprs = extract_exprs(expressions)?;
        Ok(PhpLazyGroupBy::new(self.inner.clone().lazy(), exprs))
    }

    // Phase 2: Missing Aggregations //

    /// Aggregate the columns to their sum value.
    pub fn sum(&self) -> ExtResult<Self> {
        let inner = self
            .inner
            .clone()
            .lazy()
            .sum()
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| PolarsException::new(format!("Cannot execute sum operation: {}", e)))?;
        Ok(Self { inner })
    }

    /// Aggregate the columns to their median value.
    pub fn median(&self) -> ExtResult<Self> {
        let inner = self
            .inner
            .clone()
            .lazy()
            .median()
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| {
                PolarsException::new(format!("Cannot execute median operation: {}", e))
            })?;
        Ok(Self { inner })
    }

    /// Aggregate the columns to their variance value.
    #[php(defaults(ddof = 0))]
    pub fn variance(&self, ddof: u8) -> ExtResult<Self> {
        let inner = self
            .inner
            .clone()
            .lazy()
            .var(ddof)
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| {
                PolarsException::new(format!("Cannot execute variance operation: {}", e))
            })?;
        Ok(Self { inner })
    }

    /// Aggregate the columns to their quantile value.
    pub fn quantile(&self, quantile: f64) -> ExtResult<Self> {
        let inner = self
            .inner
            .clone()
            .lazy()
            .quantile(lit(quantile), QuantileMethod::Nearest)
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| {
                PolarsException::new(format!("Cannot execute quantile operation: {}", e))
            })?;
        Ok(Self { inner })
    }

    /// Aggregate the columns to their null count.
    #[php(name = "nullCount")]
    pub fn null_count(&self) -> ExtResult<Self> {
        let inner = self
            .inner
            .clone()
            .lazy()
            .null_count()
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| {
                PolarsException::new(format!("Cannot execute nullCount operation: {}", e))
            })?;
        Ok(Self { inner })
    }

    /// Aggregate the columns to their product value.
    pub fn product(&self) -> ExtResult<Self> {
        let inner = self
            .inner
            .clone()
            .lazy()
            .select(&[Expr::from(all()).product()])
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| {
                PolarsException::new(format!("Cannot execute product operation: {}", e))
            })?;
        Ok(Self { inner })
    }

    // Phase 3: Row/Column Manipulation //

    /// Get unique rows
    /// @param string[]|null $subset Column names to consider for uniqueness
    #[php(defaults(keep = "first".to_string()))]
    pub fn unique(&self, subset: Option<Vec<String>>, keep: String) -> ExtResult<Self> {
        let strategy = match keep.as_str() {
            "first" => UniqueKeepStrategy::First,
            "last" => UniqueKeepStrategy::Last,
            "any" => UniqueKeepStrategy::Any,
            "none" => UniqueKeepStrategy::None,
            _ => {
                return Err(PolarsException::new(format!(
                    "Invalid keep strategy: {}. Use 'first', 'last', 'any', or 'none'",
                    keep
                )));
            }
        };
        let selector = subset.map(|cols| {
            let names: Vec<PlSmallStr> = cols.into_iter().map(PlSmallStr::from).collect();
            Selector::ByName {
                names: names.into(),
                strict: false,
            }
        });
        let inner = self
            .inner
            .clone()
            .lazy()
            .unique(selector, strategy)
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| PolarsException::new(format!("Unique failed: {}", e)))?;
        Ok(Self { inner })
    }

    /// Drop rows with null values
    /// @param string[]|null $subset Column names to check
    #[php(name = "dropNulls")]
    pub fn drop_nulls(&self, subset: Option<Vec<String>>) -> ExtResult<Self> {
        let selector = subset.map(|cols| {
            let names: Vec<PlSmallStr> = cols.into_iter().map(PlSmallStr::from).collect();
            Selector::ByName {
                names: names.into(),
                strict: false,
            }
        });
        let inner = self
            .inner
            .clone()
            .lazy()
            .drop_nulls(selector)
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| PolarsException::new(format!("dropNulls failed: {}", e)))?;
        Ok(Self { inner })
    }

    /// Fill null values with a value or expression
    /// @param int|float|string|bool|null|\Polars\Expr $value
    #[php(name = "fillNull")]
    pub fn fill_null(&self, value: &Zval) -> ExtResult<Self> {
        let fill_expr = crate::expression::zval_to_expr(value)?;
        let inner = self
            .inner
            .clone()
            .lazy()
            .fill_null(fill_expr)
            .collect()
            .map_err(|e| PolarsException::new(format!("fillNull failed: {}", e)))?;
        Ok(Self { inner })
    }

    /// Fill NaN values with a value or expression
    /// @param int|float|string|bool|null|\Polars\Expr $value
    #[php(name = "fillNan")]
    pub fn fill_nan(&self, value: &Zval) -> ExtResult<Self> {
        let fill_expr = crate::expression::zval_to_expr(value)?;
        let inner = self
            .inner
            .clone()
            .lazy()
            .fill_nan(fill_expr)
            .collect()
            .map_err(|e| PolarsException::new(format!("fillNan failed: {}", e)))?;
        Ok(Self { inner })
    }

    /// Reverse row order
    pub fn reverse(&self) -> ExtResult<Self> {
        let inner = self
            .inner
            .clone()
            .lazy()
            .reverse()
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| PolarsException::new(format!("Reverse failed: {}", e)))?;
        Ok(Self { inner })
    }

    /// Get a slice of rows
    pub fn slice(&self, offset: i64, length: i64) -> Self {
        Self {
            inner: self.inner.slice(offset, length as usize),
        }
    }

    /// Limit to n rows (alias for head)
    #[php(defaults(n = 10))]
    pub fn limit(&self, n: i32) -> Self {
        self.head(n)
    }

    /// Join with another DataFrame
    /// @param \Polars\DataFrame $other The right DataFrame
    /// @param \Polars\Expr[] $on Join columns (used for both left and right)
    /// @param string $how Join type: 'inner', 'left', 'right', 'full', 'cross'
    #[php(defaults(how = "inner".to_string()))]
    pub fn join(&self, other: &PhpDataFrame, on: &ZendHashTable, how: String) -> ExtResult<Self> {
        let exprs = extract_exprs(on)?;
        let join_type = match how.as_str() {
            "inner" => JoinType::Inner,
            "left" => JoinType::Left,
            "right" => JoinType::Right,
            "full" => JoinType::Full,
            "cross" => JoinType::Cross,
            _ => {
                return Err(PolarsException::new(format!(
                    "Invalid join type: {}. Use 'inner', 'left', 'right', 'full', or 'cross'",
                    how
                )));
            }
        };
        let inner = self
            .inner
            .clone()
            .lazy()
            .join(
                other.inner.clone().lazy(),
                &exprs,
                &exprs,
                JoinArgs::new(join_type),
            )
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| PolarsException::new(format!("Join failed: {}", e)))?;
        Ok(Self { inner })
    }

    /// Add a row index column
    #[php(name = "withRowIndex")]
    #[php(defaults(name = "index".to_string(), offset = 0))]
    pub fn with_row_index(&self, name: String, offset: i64) -> ExtResult<Self> {
        let inner = self
            .inner
            .clone()
            .lazy()
            .with_row_index(PlSmallStr::from(name), Some(offset as u32))
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| PolarsException::new(format!("withRowIndex failed: {}", e)))?;
        Ok(Self { inner })
    }

    // Phase 4: Export/Row Access //

    /// Convert DataFrame to a PHP array of associative arrays (rows)
    #[php(name = "toArray")]
    pub fn to_array(&self) -> ExtResult<Vec<HashMap<String, Zval>>> {
        let height = self.inner.height();
        let columns = self.inner.get_columns();
        let mut result = Vec::with_capacity(height);

        for row_idx in 0..height {
            let mut row = HashMap::new();
            for col_item in columns {
                let col_name = col_item.name().to_string();
                let value = col_item
                    .get(row_idx)
                    .map_err(|e| PolarsException::new(format!("Failed to get value: {}", e)))?;
                let zval = any_value_to_zval(value)?;
                row.insert(col_name, zval);
            }
            result.push(row);
        }
        Ok(result)
    }

    /// Get a single row as an associative array (supports negative indexing)
    pub fn row(&self, index: i64) -> ExtResult<HashMap<String, Zval>> {
        let idx = if index < 0 {
            (self.inner.height() as i64 + index) as usize
        } else {
            index as usize
        };
        if idx >= self.inner.height() {
            return Err(PolarsException::new(format!(
                "Row index {} out of bounds for DataFrame with {} rows",
                index,
                self.inner.height()
            )));
        }
        let columns = self.inner.get_columns();
        let mut row = HashMap::new();
        for col_item in columns {
            let col_name = col_item.name().to_string();
            let value = col_item
                .get(idx)
                .map_err(|e| PolarsException::new(format!("Failed to get value: {}", e)))?;
            let zval = any_value_to_zval(value)?;
            row.insert(col_name, zval);
        }
        Ok(row)
    }

    /// Get all rows as array of associative arrays (alias for toArray)
    pub fn rows(&self) -> ExtResult<Vec<HashMap<String, Zval>>> {
        self.to_array()
    }

    // Phase 5: Direct DataFrame Methods //

    /// Grow this DataFrame vertically by stacking another DataFrame
    /// @param \Polars\DataFrame $other
    pub fn vstack(&self, other: &PhpDataFrame) -> ExtResult<Self> {
        let mut df = self.inner.clone();
        df.vstack_mut(&other.inner)
            .map_err(|e| PolarsException::new(format!("vstack failed: {}", e)))?;
        Ok(Self { inner: df })
    }

    /// Grow this DataFrame horizontally by adding Series columns
    /// @param \Polars\Series[] $columns
    pub fn hstack(&self, columns: &ZendHashTable) -> ExtResult<Self> {
        let mut series_vec: Vec<Column> = Vec::new();
        for (_, value) in columns.iter() {
            let series: &PhpSeries = match value.extract::<&PhpSeries>() {
                Some(s) => s,
                None => {
                    return Err(PolarsException::new(
                        "Argument must be a list of \\Polars\\Series objects".to_string(),
                    ));
                }
            };
            series_vec.push(Column::from(series.inner.clone()));
        }
        let df = self
            .inner
            .hstack(&series_vec)
            .map_err(|e| PolarsException::new(format!("hstack failed: {}", e)))?;
        Ok(Self { inner: df })
    }

    /// Check if two DataFrames are equal
    /// @param \Polars\DataFrame $other
    pub fn equals(&self, other: &PhpDataFrame) -> bool {
        self.inner.equals(&other.inner)
    }

    /// Get the estimated size in bytes
    #[php(name = "estimatedSize")]
    pub fn estimated_size(&self) -> usize {
        self.inner.estimated_size()
    }

    /// Get the column index by name, returns -1 if not found
    #[php(name = "getColumnIndex")]
    pub fn get_column_index(&self, name: String) -> i64 {
        match self.inner.get_column_index(&name) {
            Some(idx) => idx as i64,
            None => -1,
        }
    }

    /// Create an empty copy of the DataFrame (same schema, no rows)
    pub fn clear(&self) -> Self {
        Self {
            inner: self.inner.clear(),
        }
    }

    /// Rechunk the DataFrame into contiguous memory
    pub fn rechunk(&self) -> Self {
        let mut df = self.inner.clone();
        df.align_chunks_par();
        Self { inner: df }
    }

    /// Shrink memory usage of the DataFrame
    /// @return void
    #[php(name = "shrinkToFit")]
    pub fn shrink_to_fit(&mut self) {
        self.inner.shrink_to_fit();
    }

    /// Get a boolean mask of duplicated rows
    /// @return \Polars\Series
    #[php(name = "isDuplicated")]
    pub fn is_duplicated(&self) -> ExtResult<PhpSeries> {
        let mask = self
            .inner
            .is_duplicated()
            .map_err(|e| PolarsException::new(format!("isDuplicated failed: {}", e)))?;
        Ok(PhpSeries::from(mask.into_series()))
    }

    /// Get a boolean mask of unique rows
    /// @return \Polars\Series
    #[php(name = "isUnique")]
    pub fn is_unique(&self) -> ExtResult<PhpSeries> {
        let mask = self
            .inner
            .is_unique()
            .map_err(|e| PolarsException::new(format!("isUnique failed: {}", e)))?;
        Ok(PhpSeries::from(mask.into_series()))
    }

    // Phase 6: Advanced Lazy Delegates //

    /// Shift column values by n positions
    pub fn shift(&self, n: i64) -> ExtResult<Self> {
        let inner = self
            .inner
            .clone()
            .lazy()
            .with_columns(&[Expr::from(all()).shift(lit(n))])
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| PolarsException::new(format!("Shift failed: {}", e)))?;
        Ok(Self { inner })
    }

    /// Take every nth row
    #[php(name = "gatherEvery", defaults(offset = 0))]
    pub fn gather_every(&self, n: i64, offset: i64) -> ExtResult<Self> {
        let inner = self
            .inner
            .clone()
            .lazy()
            .select(&[Expr::from(all()).gather_every(n as usize, offset as usize)])
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| PolarsException::new(format!("gatherEvery failed: {}", e)))?;
        Ok(Self { inner })
    }

    /// Cast columns to different data types
    /// @param array $dtypes Associative array of column name => data type string
    #[php(defaults(strict = false))]
    pub fn cast(&self, dtypes: &ZendHashTable, strict: bool) -> ExtResult<Self> {
        let mut exprs: Vec<Expr> = Vec::new();
        for (key, value) in dtypes.iter() {
            let col_name = match key {
                ArrayKey::String(s) => s,
                ArrayKey::Str(s) => s.to_string(),
                ArrayKey::Long(i) => i.to_string(),
            };
            let dtype_str = value.string().ok_or_else(|| {
                PolarsException::new(format!("Data type for column '{}' must be a string", col_name))
            })?;
            let target_type = parse_dtype(&dtype_str)?;
            if strict {
                exprs.push(col(&col_name).strict_cast(target_type));
            } else {
                exprs.push(col(&col_name).cast(target_type));
            }
        }
        let inner = self
            .inner
            .clone()
            .lazy()
            .with_columns(&exprs)
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| PolarsException::new(format!("Cast failed: {}", e)))?;
        Ok(Self { inner })
    }

    /// Unpivot a DataFrame from wide to long format
    /// @param string[] $on Columns to use as values
    /// @param string[] $index Columns to use as identifier
    pub fn unpivot(&self, on: Vec<String>, index: Vec<String>) -> ExtResult<Self> {
        use polars::prelude::UnpivotArgsDSL;
        let on_names: Vec<PlSmallStr> = on.into_iter().map(PlSmallStr::from).collect();
        let index_names: Vec<PlSmallStr> = index.into_iter().map(PlSmallStr::from).collect();
        let args = UnpivotArgsDSL {
            on: Selector::ByName {
                names: on_names.into(),
                strict: false,
            },
            index: Selector::ByName {
                names: index_names.into(),
                strict: false,
            },
            variable_name: None,
            value_name: None,
        };
        let inner = self
            .inner
            .clone()
            .lazy()
            .unpivot(args)
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| PolarsException::new(format!("Unpivot failed: {}", e)))?;
        Ok(Self { inner })
    }

    /// Explode list columns into rows
    /// @param string[] $columns Column names to explode
    pub fn explode(&self, columns: Vec<String>) -> ExtResult<Self> {
        let names: Vec<PlSmallStr> = columns.into_iter().map(PlSmallStr::from).collect();
        let selector = Selector::ByName {
            names: names.into(),
            strict: false,
        };
        let inner = self
            .inner
            .clone()
            .lazy()
            .explode(selector)
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| PolarsException::new(format!("Explode failed: {}", e)))?;
        Ok(Self { inner })
    }

    // Phase 7: Descriptive Methods //

    /// Get schema description as string
    #[php(getter)]
    pub fn get_schema(&self) -> String {
        format!("{:?}", self.inner.schema())
    }

    /// Get the number of unique values per column
    #[php(name = "nUnique")]
    pub fn n_unique(&self) -> ExtResult<Self> {
        let inner = self
            .inner
            .clone()
            .lazy()
            .select(&[Expr::from(all()).n_unique()])
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| PolarsException::new(format!("nUnique failed: {}", e)))?;
        Ok(Self { inner })
    }

    /// Get a quick summary of the DataFrame
    pub fn glimpse(&self) -> String {
        let columns = self.inner.get_columns();
        let height = self.inner.height();
        let width = self.inner.width();
        let mut result = format!("Rows: {}\nColumns: {}\n", height, width);

        for col_item in columns {
            let name = col_item.name();
            let dtype = col_item.dtype();
            let preview: Vec<String> = (0..std::cmp::min(5, height))
                .map(|i| {
                    col_item
                        .get(i)
                        .map(|v| format!("{}", v))
                        .unwrap_or_else(|_| "?".to_string())
                })
                .collect();
            result.push_str(&format!(
                "$ {:15} {:10} {}\n",
                name,
                format!("{}", dtype),
                preview.join(", ")
            ));
        }
        result
    }

    /// Get descriptive statistics (count, null_count, mean, std, min, max, median)
    pub fn describe(&self) -> ExtResult<Self> {

        let lazy = self.inner.clone().lazy();

        // Helper to collect a stat row and cast numeric columns to f64 strings
        let collect_stat = |lf: polars::prelude::LazyFrame| -> ExtResult<DataFrame> {
            lf.with_optimizations(OptFlags::EAGER)
                .collect()
                .map_err(|e| PolarsException::new(format!("Describe failed: {}", e)))
        };

        let count_df = collect_stat(lazy.clone().count())?;
        let null_count_df = collect_stat(lazy.clone().null_count())?;
        let mean_df = collect_stat(lazy.clone().mean())?;
        let std_df = collect_stat(lazy.clone().std(1))?;
        let min_df = collect_stat(lazy.clone().min())?;
        let max_df = collect_stat(lazy.clone().max())?;
        let median_df = collect_stat(lazy.clone().median())?;

        let stat_names = ["count", "null_count", "mean", "std", "min", "max", "median"];
        let stat_dfs = [&count_df, &null_count_df, &mean_df, &std_df, &min_df, &max_df, &median_df];

        // Build result: first column is "statistic", then each original column cast to string
        let mut result_columns: Vec<Column> = Vec::new();
        result_columns.push(Column::new(
            "statistic".into(),
            stat_names.iter().map(|s| s.to_string()).collect::<Vec<String>>(),
        ));

        for col_name in self.inner.get_column_names() {
            let mut values: Vec<String> = Vec::new();
            for stat_df in &stat_dfs {
                let col_item = stat_df.column(col_name.as_str()).map_err(|e| {
                    PolarsException::new(format!("Describe failed: {}", e))
                })?;
                let val = col_item.get(0).map_err(|e| {
                    PolarsException::new(format!("Describe failed: {}", e))
                })?;
                values.push(format!("{}", val));
            }
            result_columns.push(Column::new(col_name.clone(), values));
        }

        let inner = DataFrame::new(result_columns)
            .map_err(|e| PolarsException::new(format!("Describe failed: {}", e)))?;
        Ok(Self { inner })
    }

    // Phase 8: Remaining Methods //

    /// Randomly sample n rows
    #[allow(non_snake_case)]
    #[php(defaults(withReplacement = false, shuffle = true))]
    pub fn sample(
        &self,
        n: i64,
        withReplacement: bool,
        shuffle: bool,
        seed: Option<i64>,
    ) -> ExtResult<Self> {
        use polars::prelude::{IdxSize, NamedFrom, Series};
        let seed = seed.map(|s| s as u64);
        let n_series = Series::new("n".into(), &[n as IdxSize]);
        let inner = self
            .inner
            .sample_n(&n_series, withReplacement, shuffle, seed)
            .map_err(|e| PolarsException::new(format!("Sample failed: {}", e)))?;
        Ok(Self { inner })
    }

    /// Transpose the DataFrame
    #[allow(non_snake_case)]
    #[php(defaults(includeHeader = false, headerName = "column".to_string()))]
    pub fn transpose(&mut self, includeHeader: bool, headerName: String) -> ExtResult<Self> {
        let inner = if includeHeader {
            self.inner
                .transpose(Some(&headerName), None)
                .map_err(|e| PolarsException::new(format!("Transpose failed: {}", e)))?
        } else {
            self.inner
                .transpose(None, None)
                .map_err(|e| PolarsException::new(format!("Transpose failed: {}", e)))?
        };
        Ok(Self { inner })
    }

    /// Get the top k rows by a column
    #[php(name = "topK")]
    pub fn top_k(&self, k: i64, by: String) -> ExtResult<Self> {
        let opts = SortMultipleOptions::new()
            .with_order_descending(true)
            .with_nulls_last(true);
        let inner = self
            .inner
            .clone()
            .lazy()
            .sort([&by], opts)
            .limit(k as u32)
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| PolarsException::new(format!("topK failed: {}", e)))?;
        Ok(Self { inner })
    }

    /// Get the bottom k rows by a column
    #[php(name = "bottomK")]
    pub fn bottom_k(&self, k: i64, by: String) -> ExtResult<Self> {
        let opts = SortMultipleOptions::new()
            .with_order_descending(false)
            .with_nulls_last(true);
        let inner = self
            .inner
            .clone()
            .lazy()
            .sort([&by], opts)
            .limit(k as u32)
            .with_optimizations(OptFlags::EAGER)
            .collect()
            .map_err(|e| PolarsException::new(format!("bottomK failed: {}", e)))?;
        Ok(Self { inner })
    }
}

/// Methods that are hidden from PHP stubs
impl PhpDataFrame {
    fn _do_select(&self, exprs: Vec<Expr>) -> ExtResult<Self> {
        match self
            .inner
            .clone()
            .lazy()
            .select(&exprs)
            .with_optimizations(OptFlags::EAGER)
            .collect()
        {
            Ok(df) => Ok(df.into()),
            Err(e) => Err(e.into()),
        }
    }
}

impl Into<PhpDataFrame> for DataFrame {
    fn into(self) -> PhpDataFrame {
        PhpDataFrame { inner: self }
    }
}
