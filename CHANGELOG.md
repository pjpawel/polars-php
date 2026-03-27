# Changelog

All notable changes to this project will be documented in this file.

## 0.5.0

### Enhanced Existing Methods
- [Feature] `sort()` — accept array of columns for multi-column sorting, add `maintainOrder` and `multithreaded` parameters
- [Feature] `join()` — add `leftOn`, `rightOn`, `suffix`, `validate`, and `coalesce` parameters
- [Feature] `unpivot()` — add `variableName` and `valueName` parameters
- [Feature] `sample()` — add `fraction` parameter for fractional sampling via `sample_frac()`
- [Feature] `transpose()` — add `columnNames` parameter to specify output column names

### New DataFrame Methods
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

## 0.4.0
- [Feature] Add missing aggregation methods for Series class: `argMax()`, `argMin()`, `nanMax()`, `nanMin()`, `maxBy()`, `minBy()`, `mode()`, `implode()`
- [Feature] Add `Series::getFlags()` method to retrieve sorting and optimization flags
- [Feature] Benchmark suite comparing polars-php vs pure PHP (PHPBench) for DataFrame creation, CSV I/O, aggregations, filtering, sorting, joins, and column access
