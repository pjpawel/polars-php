use ext_php_rs::prelude::*;
use polars::prelude::{Column, CsvWriter, DataFrame, IntoLazy, OptFlags, SerWriter};
use ext_php_rs::types::{ArrayKey, ZendHashTable, Zval};
use ext_php_rs::flags::{DataType as PhpDataType};
use polars_plan::dsl::{Expr};
use crate::data_type::PolarsDataType;
use crate::exception::{ExtResult, PolarsException};
use crate::expression::PolarsExpr;


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
                return Err(PolarsException::new(format!("Column '{}' must be an array", col_name)));
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
            return Ok(Column::new(name.into(), col_values))
        }
    };
    match first_value.get_type() {
        PhpDataType::Bool => {
            let col_values: Vec<Option<bool>> = values
                .iter()
                .map(|v: &Zval| v.bool()) // returns Option<bool>
                .collect();
            Ok(Column::new(name.into(), col_values))
        },
        PhpDataType::Long => {
            let col_values: Vec<Option<i64>> = values
                .iter()
                .map(|v: &Zval| {
                    match v.long() {
                        Some(v) => Some(v as i64),
                        None => None
                    }
                })
                .collect();
            Ok(Column::new(name.into(), col_values))
        }
        PhpDataType::Double => {
            let col_values: Vec<Option<f64>> = values
                .iter()
                .map(|v: &Zval| {
                    match v.double() {
                        Some(v) => Some(v as f64),
                        None => None
                    }
                })
                .collect();
            Ok(Column::new(name.into(), col_values))
        }
        PhpDataType::String => {
            let col_values: Vec<Option<String>> = values
                .iter()
                .map(|v| v.string())
                .collect();
            Ok(Column::new(name.into(), col_values))
        },
        PhpDataType::Null => {
            let col_values: Vec<Option<String>> = vec![None; values.len()];
            Ok(Column::new(name.into(), col_values))
        }
        default => {
            Err(PolarsException::new(format!("Unsupported type '{}' for column '{}'", first_value.get_type(), name)))
        }
    }
}


#[php_class]
#[php(name = "Polars\\DataFrame")]
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
    #[php(defaults(by_keys = true))]
    pub fn __construct(data: &ZendHashTable, byKeys: bool) -> ExtResult<Self> {
        let col_vec = match data.is_empty() {
            true => Vec::new(),
            false => match by_keys {
                true => parse_array_to_cols_by_keys(data)?,
                false => return Err(PolarsException::new("Parsing data by first row header is currently not implemented".to_string()))
            }
        };
        let df = DataFrame::new(col_vec)
            .map_err(|e| PolarsException::new(format!("Failed to create DataFrame: {}", e)))?;
        Ok(Self { inner: df })
    }


    // Attributes //

    /// Get columns names
    #[php(getter)]
    pub fn get_columns(&self) -> Vec<&str> {
        self.inner
            .get_column_names()
            .iter()
            .map(|c| c.as_str())
            .collect()
    }

    /// Set columns names
    /// @param string[] $columns - length of list must be equal to current length of columns
    #[php(setter)]
    pub fn set_columns(&mut self, columns: Vec<&str>) -> ExtResult<()> {
        Ok(self.inner.set_column_names(columns).map_err(|e| {
            PolarsException::new(format!("Failed to set DataFrame column names: {}", e))
        })?)
    }

    /// Return list of
    #[php(getter)]
    pub fn dtypes(&self) -> Vec<PolarsDataType> {
        self.inner
            .dtypes()
            .iter()
            .map(|d| d.clone().into())
            .collect()
    }

    /// Get the number of rows
    pub fn height(&self) -> usize {
        self.inner.height()
    }

    /// Get the shape of the DataFrame as [rows, columns]
    pub fn shape(&self) -> Vec<usize> {
        vec![self.inner.height(), self.inner.width()]
    }

    /// Get the number of columns
    pub fn width(&self) -> usize {
        self.inner.width()
    }

    // Aggregation //

    /// Return the number of non-null elements for each column.
    pub fn count(&self) -> ExtResult<Self> {
        let inner = match self.inner.clone().lazy()
            .count()
            .with_optimizations(OptFlags::EAGER)
            .collect() {
            Ok(v) => v,
            Err(e) => return Err(PolarsException::new(format!("Cannot execute count operation: {}", e.to_string()))),
        };
        Ok(Self { inner })
    }

    /// Aggregate the columns of this DataFrame to their maximum value.
    pub fn max(&self) -> ExtResult<Self> {
        let inner = match self.inner.clone().lazy()
            .max()
            .with_optimizations(OptFlags::EAGER)
            .collect() {
            Ok(v) => v,
            Err(e) => return Err(PolarsException::new(format!("Cannot execute max operation: {}", e.to_string()))),
        };
        Ok(Self { inner })
    }

    /// Get the maximum value horizontally across columns.
    pub fn max_horizontal(&self) -> ExtResult<Self> {
        //use select https://github.com/pola-rs/polars/blob/py-1.35.2/py-polars/src/polars/dataframe/frame.py#L10422
        todo!()
    }

    /// Aggregate the columns of this DataFrame to their mean value.
    pub fn mean(&self) -> ExtResult<Self> {
        let inner = match self.inner.clone().lazy()
            .mean()
            .with_optimizations(OptFlags::EAGER)
            .collect() {
            Ok(v) => v,
            Err(e) => return Err(PolarsException::new(format!("Cannot execute min operation: {}", e.to_string()))),
        };
        Ok(Self { inner })
    }

    /// Aggregate the columns of this DataFrame to their minimal value.
    pub fn min(&self) -> ExtResult<Self> {
        let inner = match self.inner.clone().lazy()
            .min()
            .with_optimizations(OptFlags::EAGER)
            .collect() {
                Ok(v) => v,
                Err(e) => return Err(PolarsException::new(format!("Cannot execute min operation: {}", e.to_string()))),
            };
        Ok(Self { inner })
    }

    /// Aggregate the columns of this DataFrame to their product value.
    //#[php(defaults(ddof = 0))]
    #[php(optional = ddof, defaults(ddof = "0"))]
    pub fn std(&self, ddof: u8) -> ExtResult<Self> {
        let inner = match self.inner.clone().lazy()
            .std(ddof) //https://numpy.org/doc/stable/reference/generated/numpy.std.html#
            .with_optimizations(OptFlags::EAGER)
            .collect() {
            Ok(v) => v,
            Err(e) => return Err(PolarsException::new(format!("Cannot execute min operation: {}", e.to_string()))),
        };
        Ok(Self { inner })
    }



    /// Make select based on given expressions
    /// @param \Polars\Expr[]
    pub fn select(&self, expressions: &ZendHashTable) -> ExtResult<Self> {
        let mut exprs: Vec<Expr> = Vec::new();
        for (_, value) in expressions.iter() {
            let expr: &PolarsExpr = match value.extract::<&PolarsExpr>() {
                Some(expr) => expr,
                None => return Err(PolarsException::new("Argument must be a list of \\Polars\\Expr objects".to_string()))
            };
            exprs.push(expr.get_expr().clone());
        }
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



    // // /// Convert DataFrame to a PHP array
    // // /// Returns array of rows, each row is an associative array
    // // pub fn to_array(&self) -> ExtResult<Vec<HashMap<String, Zval>>> {
    // //     let mut result = Vec::new();
    // //     let height = self.inner.height();
    // //     let columns = self.inner.get_columns();
    // //
    // //     for row_idx in 0..height {
    // //         let mut row_map = HashMap::new();
    // //
    // //         for col in columns {
    // //             let col_name = col.name().to_string();
    // //             let value = Self::series_value_to_zval(col, row_idx)?;
    // //             row_map.insert(col_name, value);
    // //         }
    // //
    // //         result.push(row_map);
    // //     }
    // //
    // //     Ok(result)
    // // }
    //
    // // /// Get a single column as a Series
    // // pub fn column(&self, name: String) -> ExtResult<PhpSeries> {
    // //     let series = self.inner
    // //         .column(&name)
    // //         .map_err(|e| PolarsException::new(format!("Column '{}' not found: {}", name, e)))?;
    // //
    // //     Ok(PhpSeries {
    // //         inner: series.clone(),
    // //     })
    // // }

    /// Display the DataFrame (returns a formatted string)
    #[php(name = "__toString")]
    pub fn __to_string(&self) -> String {
        format!("{}", self.inner)
    }

    /// Write to CSV file
    #[php(defaults(include_headers = true))]
    #[allow(non_snake_case)]
    pub fn write_csv(&self, path: String, includeHeader: bool) -> ExtResult<()> {
        let mut file = std::fs::File::create(&path)
            .map_err(|e| PolarsException::new(format!("Failed to create file: {}", e)))?;

        CsvWriter::new(&mut file)
            .include_header(includeHeader)
            .finish(&mut self.inner.clone())
            .map_err(|e| PolarsException::new(format!("Failed to write CSV: {}", e)))?;

        Ok(())
    }

    // /// Sort DataFrame by column(s)
    // ///
    // /// # Example (PHP)
    // /// ```php
    // /// $df = $df->sort('age');
    // /// $df = $df->sort(['city', 'age'], descending: true);
    // /// ```
    // pub fn sort(
    //     &self,
    //     by: Vec<String>,
    //     #[php_default(false)] descending: bool,
    // ) -> ExtResult<Self> {
    //     let mut df = self.inner.clone();
    //
    //     for col in by.iter().rev() {
    //         df = df
    //             .sort([col], descending, false)
    //             .map_err(|e| PolarsException::new(format!("Sort failed: {}", e)))?;
    //     }
    //
    //     Ok(Self { inner: df })
    // }

    // /// Drop specified columns
    // pub fn drop(&self, columns: Vec<String>) -> ExtResult<Self> {
    //     let df = self.inner
    //         .drop_many(&columns)
    //         .map_err(|e| PolarsException::new(format!("Drop failed: {}", e)))?;
    //
    //     Ok(Self { inner: df })
    // }

    // /// Rename columns
    // ///
    // /// # Example (PHP)
    // /// ```php
    // /// $df = $df->rename(['old_name' => 'new_name', 'age' => 'years']);
    // /// ```
    // pub fn rename(&self, mapping: HashMap<String, String>) -> ExtResult<Self> {
    //     let mut df = self.inner.clone();
    //
    //     for (old_name, new_name) in mapping {
    //         df = df
    //             .rename(&old_name, &new_name)
    //             .map_err(|e| PolarsException::new(format!("Rename failed: {}", e)))?;
    //     }
    //
    //     Ok(Self { inner: df })
    // }
    //
    // /// Get descriptive statistics
    // pub fn describe(&self) -> ExtResult<Self> {
    //     let stats_df = self.inner
    //         .describe(None)
    //         .map_err(|e| PolarsException::new(format!("Describe failed: {}", e)))?;
    //
    //     Ok(Self { inner: stats_df })
    // }
}

/// Methods that are hidden from PHP stubs
impl PhpDataFrame {

    fn _do_select(&self, exprs: Vec<Expr>) -> ExtResult<Self> {
        match self.inner.clone().lazy()
            .select(&exprs)
            .with_optimizations(OptFlags::EAGER)
            .collect() {
            Ok(df) => Ok(df.into()),
            Err(e) => Err(e.into())
        }
    }


}

impl Into<PhpDataFrame> for DataFrame {
    fn into(self) -> PhpDataFrame {
        PhpDataFrame { inner: self }
    }
}

// #[php_class]
// // #[php(name = "Series")]
// #[derive(Clone)]
// pub struct PhpSeries {
//     pub inner: Series,
// }
//
// #[php_impl]
// impl PhpSeries {
//     /// Get the name of the Series
//     pub fn name(&self) -> String {
//         self.inner.name().to_string()
//     }
//
//     /// Get the length of the Series
//     pub fn len(&self) -> usize {
//         self.inner.len()
//     }
//
//     /// Get the data type
//     pub fn dtype(&self) -> String {
//         format!("{:?}", self.inner.dtype())
//     }
//
//     // /// Convert Series to PHP array
//     // pub fn to_array(&self) -> ExtResult<Vec<Zval>> {
//     //     let mut result = Vec::new();
//     //
//     //     for idx in 0..self.inner.len() {
//     //         let value = PhpDataFrame::series_value_to_zval(&self.inner, idx)?;
//     //         result.push(value);
//     //     }
//     //
//     //     Ok(result)
//     // }
//
//     /// Get sum (for numeric series)
//     pub fn sum(&self) -> ExtResult<f64> {
//         match self.inner.sum::<f64>() {
//             Some(sum) => Ok(sum),
//             None => Err(PolarsException::new("Cannot compute sum for this type".to_string())),
//         }
//     }
//
//     /// Get mean (for numeric series)
//     pub fn mean(&self) -> ExtResult<f64> {
//         self.inner
//             .mean()
//             .ok_or_else(|| PolarsException::new("Cannot compute mean for this type".to_string()))
//     }
//
//     /// Get median (for numeric series)
//     pub fn median(&self) -> ExtResult<f64> {
//         self.inner
//             .median()
//             .ok_or_else(|| PolarsException::new("Cannot compute median for this type".to_string()))
//     }
//
//     /// Get standard deviation
//     pub fn std(&self) -> ExtResult<f64> {
//         self.inner
//             .std(1)
//             .ok_or_else(|| PolarsException::new("Cannot compute std for this type".to_string()))
//     }
//
//     // /// Get minimum value
//     // pub fn min(&self) -> ExtResult<Zval> {
//     //     let min_val = self.inner
//     //         .min::<f64>()
//     //         .ok_or_else(|| PolarsException::new("Cannot compute min".to_string()))?;
//     //     Ok(Zval::from(min_val))
//     // }
//     //
//     // /// Get maximum value
//     // pub fn max(&self) -> ExtResult<Zval> {
//     //     let max_val = self.inner
//     //         .max::<f64>()
//     //         .ok_or_else(|| PolarsException::new("Cannot compute max".to_string()))?;
//     //     Ok(Zval::from(max_val))
//     // }
//
//     /// Count non-null values
//     pub fn count(&self) -> usize {
//         self.inner.len() - self.inner.null_count()
//     }
//
//     /// Count null values
//     pub fn null_count(&self) -> usize {
//         self.inner.null_count()
//     }
//
//     /// Display the Series
//     pub fn __toString(&self) -> String {
//         format!("{}", self.inner)
//     }
// }



// // Helper function to convert a Series value to Zval
// fn series_value_to_zval(series: &Series, idx: usize) -> ExtResult<Zval> {
//     let any_value = series
//         .get(idx)
//         .map_err(|e| PolarsException::new(format!("Failed to get value: {}", e)))?;
//
//     match any_value {
//         AnyValue::Null => Ok(Zval::new()),
//         AnyValue::Boolean(b) => Ok(Zval::from(b)),
//         //AnyValue::Int8(i) => Ok(Zval::from(i as i64)),
//         AnyValue::Int8(i) => Ok(Zval::),
//         AnyValue::Int16(i) => Ok(Zval::from(i as i64)),
//         AnyValue::Int32(i) => Ok(Zval::from(i as i64)),
//         AnyValue::Int64(i) => Ok(Zval::from(i)),
//         AnyValue::UInt8(u) => Ok(Zval::from(u as i64)),
//         AnyValue::UInt16(u) => Ok(Zval::from(u as i64)),
//         AnyValue::UInt32(u) => Ok(Zval::from(u as i64)),
//         AnyValue::UInt64(u) => Ok(Zval::from(u as i64)),
//         AnyValue::Float32(f) => Ok(Zval::from(f as f64)),
//         AnyValue::Float64(f) => Ok(Zval::from(f)),
//         AnyValue::String(s) => Ok(Zval::from(s)),
//         AnyValue::StringOwned(s) => Ok(Zval::from(s.to_string())),
//         _ => Ok(Zval::from(format!("{}", any_value))),
//     }
// }
