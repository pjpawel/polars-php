# Changelog

All notable changes to this project will be documented in this file.


## 0.6.0

### New Expr aggregation methods
- [Feature] `Expr::approxNUnique()` — Approximate count of unique values
- [Feature] `Expr::argMax()` — Get the index of the maximum value
- [Feature] `Expr::argMin()` — Get the index of the minimum value
- [Feature] `Expr::bitwiseAnd()` — Bitwise AND aggregation reduction
- [Feature] `Expr::bitwiseOr()` — Bitwise OR aggregation reduction
- [Feature] `Expr::bitwiseXor()` — Bitwise XOR aggregation reduction
- [Feature] `Expr::implode()` — Aggregate values into a list
- [Feature] `Expr::kurtosis()` — Compute excess kurtosis
- [Feature] `Expr::mode()` — Compute the most frequent value
- [Feature] `Expr::quantile()` — Compute quantile with interpolation method
- [Feature] `Expr::skew()` — Compute skewness
- [Feature] `Expr::uniqueCounts()` — Return count of unique values per value
- [Feature] Add `Polars\QuantileMethod` enum for quantile interpolation

## 0.5.0

### Enhanced existing methods
- [Feature] `sort()` — accept array of columns for multi-column sorting, add `maintainOrder` and `multithreaded` parameters
- [Feature] `join()` — add `leftOn`, `rightOn`, `suffix`, `validate`, and `coalesce` parameters
- [Feature] `unpivot()` — add `variableName` and `valueName` parameters
- [Feature] `sample()` — add `fraction` parameter for fractional sampling via `sample_frac()`
- [Feature] `transpose()` — add `columnNames` parameter to specify output column names

### New methods
- [Feature] `toSeries()` — convert single-column DataFrame to Series
- [Feature] `melt()` — alias for `unpivot()` (deprecated name)
- [Feature] `dropInPlace()` — remove and return a column as Series
- [Feature] `replaceColumn()` — replace a column at a given index
- [Feature] `insertColumn()` — insert a column at a given index
- [Feature] `extend()` — append rows from another DataFrame in-place
- [Feature] `selectSeq()` — sequential (non-parallel) version of `select()`
- [Feature] `withColumnsSeq()` — sequential (non-parallel) version of `withColumns()`
- [Feature] `withRowCount()` — deprecated alias for `withRowIndex()`
- [Feature] `setSorted()` — set the sorted flag on a column
- [Feature] `dropNans()` — drop rows containing NaN values
- [Feature] `remove()` — remove a row by index (supports negative indexing)
- [Feature] `toDummies()` — create dummy/one-hot encoded columns
- [Feature] `partitionBy()` — split DataFrame into partitions by column values
- [Feature] `interpolate()` — linearly interpolate null values in all columns
- [Feature] `mergeSorted()` — merge two sorted DataFrames by a key column
- [Feature] `pivot()` — pivot (reshape long to wide) with optional aggregation
- [Feature] `unnest()` — unnest struct columns into separate columns
- [Feature] `joinWhere()` — join with arbitrary predicate expressions (inequality join)
- [Feature] `joinAsof()` — as-of join with strategy (backward/forward/nearest), by columns, and tolerance
- [Feature] `sql()` — execute SQL queries against the DataFrame
- [Feature] Add `Expr::and_()` and `Expr::or_()` bitwise AND/OR operators

## 0.4.0
- [Feature] Add missing aggregation methods for Series class: `argMax()`, `argMin()`, `nanMax()`, `nanMin()`, `maxBy()`, `minBy()`, `mode()`, `implode()`
- [Feature] Add `Series::getFlags()` method to retrieve sorting and optimization flags
- [Feature] Benchmark suite comparing polars-php vs pure PHP (PHPBench) for DataFrame creation, CSV I/O, aggregations, filtering, sorting, joins, and column access
