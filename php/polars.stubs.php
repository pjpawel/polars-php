<?php

// Stubs for polars-php

namespace Polars {
    class Exception extends \Exception {
        public function __construct() {}
    }

    class DataFrame implements \ArrayAccess {
        /**
         * @return \Polars\DataType[]
         */
        public $dtypes;

        /**
         * Get schema description as string
         */
        public $schema;

        /**
         * Get columns names
         * @returns string[]
         */
        public $columns;

        /**
         * Convert this DataFrame to a LazyFrame for lazy evaluation
         * @return \Polars\LazyFrame
         */
        public function lazy(): \Polars\LazyFrame {}

        /**
         * Check if an offset (column name) exists
         */
        public function offsetExists(mixed $offset): bool {}

        /**
         * Get value at offset
         *
         * Supports:
         * - String offset: returns single column as DataFrame $df['col1']
         * - Integer offset: returns single row as DataFrame $df[1]
         * - Array of strings: returns DataFrame with specified columns $df[['col1', 'col2']]
         * - Array of string and integer: returns specific cells $df[['col1', 1]], $df[['col1', 'col2', 0]]
         *
         * @param $offset string|int|array
         */
        public function offsetGet(mixed $offset): \Polars\DataFrame {}

        /**
         * Set value at offset - not supported for DataFrames
         * @return void
         */
        public function offsetSet(mixed $_offset, mixed $_value): void {}

        /**
         * Unset value at offset - not supported for DataFrames
         * @return void
         */
        public function offsetUnset(mixed $_offset): void {}

        /**
         * @return int Get the number of rows
         */
        public function height(): int {}

        /**
         * @return int[] Get the shape of the DataFrame as [rows, columns]
         */
        public function shape(): array {}

        /**
         * @return int Get the number of columns
         */
        public function width(): int {}

        /**
         * @return \Polars\DataFrame Return the number of non-null elements for each column.
         */
        public function count(): \Polars\DataFrame {}

        /**
         * Aggregate the columns of this DataFrame to their maximum value.
         */
        public function max(): \Polars\DataFrame {}

        /**
         * Aggregate the columns of this DataFrame to their mean value.
         */
        public function mean(): \Polars\DataFrame {}

        /**
         * Aggregate the columns of this DataFrame to their minimal value.
         */
        public function min(): \Polars\DataFrame {}

        /**
         * Aggregate the columns of this DataFrame to their product value.
         */
        public function std(int $ddof = 0): \Polars\DataFrame {}

        /**
         * Make select based on given expressions
         * @param \Polars\Expr[]
         */
        public function select(array $expressions): \Polars\DataFrame {}

        /**
         * Get the first n rows
         */
        public function head(int $n = 10): \Polars\DataFrame {}

        /**
         * Get the last n rows
         */
        public function tail(int $n = 10): \Polars\DataFrame {}

        /**
         * Return the DataFrame as a scalar value
         * The DataFrame must contain exactly one element (1 row, 1 column)
         */
        public function item(): mixed {}

        /**
         * Get a single column as a Series
         * @return \Polars\Series
         */
        public function column(string $name): \Polars\Series {}

        /**
         * Get all columns as an array of Series
         * @return \Polars\Series[]
         */
        public function getSeries(): array {}

        /**
         * Check if DataFrame is empty
         */
        public function isEmpty(): bool {}

        /**
         * Create a copy of the DataFrame
         */
        public function copy(): \Polars\DataFrame {}

        /**
         * Display the DataFrame (returns a formatted string)
         */
        public function __toString(): string {}

        /**
         * Read a DataFrame from a CSV file
         */
        public static function readCsv(string $path, bool $hasHeader = true, string $separator = ","): \Polars\DataFrame {}

        /**
         * Read a DataFrame from a JSON file
         */
        public static function readJson(string $path): \Polars\DataFrame {}

        /**
         * Read a DataFrame from a NDJSON (newline-delimited JSON) file
         */
        public static function readNdjson(string $path): \Polars\DataFrame {}

        /**
         * Read a DataFrame from a Parquet file
         */
        public static function readParquet(string $path): \Polars\DataFrame {}

        /**
         * Write to CSV file
         */
        public function writeCsv(string $path, bool $includeHeader, string $separator = ","): void {}

        /**
         * Write DataFrame to a JSON file
         */
        public function writeJson(string $path): void {}

        /**
         * Write DataFrame to a NDJSON (newline-delimited JSON) file
         */
        public function writeNdjson(string $path): void {}

        /**
         * Write DataFrame to a Parquet file
         */
        public function writeParquet(string $path): void {}

        /**
         * Sort DataFrame by a column
         */
        public function sort(string $column, bool $descending = false, bool $nullsLast = true): \Polars\DataFrame {}

        /**
         * Drop specified columns
         * @param string[] $columns
         */
        public function drop(array $columns): \Polars\DataFrame {}

        /**
         * Rename columns
         * @param string[] $existing Old column names
         * @param string[] $newNames New column names
         */
        public function rename(array $existing, array $newNames): \Polars\DataFrame {}

        /**
         * Filter rows by expression
         * @param \Polars\Expr $expression
         */
        public function filter(\Polars\Expr $expression): \Polars\DataFrame {}

        /**
         * Add or modify columns
         * @param \Polars\Expr[] $expressions
         */
        public function withColumns(array $expressions): \Polars\DataFrame {}

        /**
         * Group by expressions
         * @param \Polars\Expr[] $expressions
         * @return \Polars\LazyGroupBy
         */
        public function groupBy(array $expressions): \Polars\LazyGroupBy {}

        /**
         * Aggregate the columns to their sum value.
         */
        public function sum(): \Polars\DataFrame {}

        /**
         * Aggregate the columns to their median value.
         */
        public function median(): \Polars\DataFrame {}

        /**
         * Aggregate the columns to their variance value.
         */
        public function variance(int $ddof = 0): \Polars\DataFrame {}

        /**
         * Aggregate the columns to their quantile value.
         */
        public function quantile(float $quantile): \Polars\DataFrame {}

        /**
         * Aggregate the columns to their null count.
         */
        public function nullCount(): \Polars\DataFrame {}

        /**
         * Aggregate the columns to their product value.
         */
        public function product(): \Polars\DataFrame {}

        /**
         * Get unique rows
         * @param string[]|null $subset Column names to consider for uniqueness
         */
        public function unique(?array $subset = null, string $keep = "first"): \Polars\DataFrame {}

        /**
         * Drop rows with null values
         * @param string[]|null $subset Column names to check
         */
        public function dropNulls(?array $subset = null): \Polars\DataFrame {}

        /**
         * Fill null values with a value or expression
         * @param int|float|string|bool|null|\Polars\Expr $value
         */
        public function fillNull(mixed $value): \Polars\DataFrame {}

        /**
         * Fill NaN values with a value or expression
         * @param int|float|string|bool|null|\Polars\Expr $value
         */
        public function fillNan(mixed $value): \Polars\DataFrame {}

        /**
         * Reverse row order
         */
        public function reverse(): \Polars\DataFrame {}

        /**
         * Get a slice of rows
         */
        public function slice(int $offset, int $length): \Polars\DataFrame {}

        /**
         * Limit to n rows (alias for head)
         */
        public function limit(int $n = 10): \Polars\DataFrame {}

        /**
         * Join with another DataFrame
         * @param \Polars\DataFrame $other The right DataFrame
         * @param \Polars\Expr[] $on Join columns (used for both left and right)
         * @param string $how Join type: 'inner', 'left', 'right', 'full', 'cross'
         */
        public function join(\Polars\DataFrame $other, array $on, string $how = "inner"): \Polars\DataFrame {}

        /**
         * Add a row index column
         */
        public function withRowIndex(string $name = "index", int $offset = 0): \Polars\DataFrame {}

        /**
         * Convert DataFrame to a PHP array of associative arrays (rows)
         */
        public function toArray(): array {}

        /**
         * Get a single row as an associative array (supports negative indexing)
         */
        public function row(int $index): array {}

        /**
         * Get all rows as array of associative arrays (alias for toArray)
         */
        public function rows(): array {}

        /**
         * Grow this DataFrame vertically by stacking another DataFrame
         * @param \Polars\DataFrame $other
         */
        public function vstack(\Polars\DataFrame $other): \Polars\DataFrame {}

        /**
         * Grow this DataFrame horizontally by adding Series columns
         * @param \Polars\Series[] $columns
         */
        public function hstack(array $columns): \Polars\DataFrame {}

        /**
         * Check if two DataFrames are equal
         * @param \Polars\DataFrame $other
         */
        public function equals(\Polars\DataFrame $other): bool {}

        /**
         * Get the estimated size in bytes
         */
        public function estimatedSize(): int {}

        /**
         * Get the column index by name, returns -1 if not found
         */
        public function getColumnIndex(string $name): int {}

        /**
         * Create an empty copy of the DataFrame (same schema, no rows)
         */
        public function clear(): \Polars\DataFrame {}

        /**
         * Rechunk the DataFrame into contiguous memory
         */
        public function rechunk(): \Polars\DataFrame {}

        /**
         * Shrink memory usage of the DataFrame
         * @return void
         */
        public function shrinkToFit(): void {}

        /**
         * Get a boolean mask of duplicated rows
         * @return \Polars\Series
         */
        public function isDuplicated(): \Polars\Series {}

        /**
         * Get a boolean mask of unique rows
         * @return \Polars\Series
         */
        public function isUnique(): \Polars\Series {}

        /**
         * Shift column values by n positions
         */
        public function shift(int $n): \Polars\DataFrame {}

        /**
         * Take every nth row
         */
        public function gatherEvery(int $n, int $offset = 0): \Polars\DataFrame {}

        /**
         * Cast columns to different data types
         * @param array $dtypes Associative array of column name => data type string
         */
        public function cast(array $dtypes, bool $strict = false): \Polars\DataFrame {}

        /**
         * Unpivot a DataFrame from wide to long format
         * @param string[] $on Columns to use as values
         * @param string[] $index Columns to use as identifier
         */
        public function unpivot(array $on, array $index): \Polars\DataFrame {}

        /**
         * Explode list columns into rows
         * @param string[] $columns Column names to explode
         */
        public function explode(array $columns): \Polars\DataFrame {}

        /**
         * Get the number of unique values per column
         */
        public function nUnique(): \Polars\DataFrame {}

        /**
         * Get a quick summary of the DataFrame
         */
        public function glimpse(): string {}

        /**
         * Get descriptive statistics (count, null_count, mean, std, min, max, median)
         */
        public function describe(): \Polars\DataFrame {}

        /**
         * Randomly sample n rows
         */
        public function sample(int $n, bool $withReplacement = false, bool $shuffle = true, ?int $seed = null): \Polars\DataFrame {}

        /**
         * Transpose the DataFrame
         */
        public function transpose(bool $includeHeader = false, string $headerName = "column"): \Polars\DataFrame {}

        /**
         * Get the top k rows by a column
         */
        public function topK(int $k, string $by): \Polars\DataFrame {}

        /**
         * Get the bottom k rows by a column
         */
        public function bottomK(int $k, string $by): \Polars\DataFrame {}

        /**
         * Create a new DataFrame from a PHP array
         * keys are column name
         *
         * # Example (PHP)
         * ```php
         * $df = new DataFrame([
         *     'name' => ['Alice', 'Bob', 'Charlie'],
         *     'age' => [25, 30, 35],
         *     'city' => ['NYC', 'LA', 'Chicago']
         * ]);
         * ```
         */
        public function __construct(array $data, bool $byKeys = true) {}
    }

    class Series implements \ArrayAccess, \Countable {
        /**
         * Get the name of the Series
         */
        public $name;

        /**
         * Get the shape of the Series as [length]
         */
        public $shape;

        /**
         * Get the data type of the Series
         */
        public $dtype;

        /**
         * Check if an index exists
         */
        public function offsetExists(mixed $offset): bool {}

        /**
         * Get value at index
         */
        public function offsetGet(mixed $offset): mixed {}

        /**
         * Set value at index - not supported
         * @return void
         */
        public function offsetSet(mixed $_offset, mixed $_value): void {}

        /**
         * Unset value at index - not supported
         * @return void
         */
        public function offsetUnset(mixed $_offset): void {}

        /**
         * Get the number of elements (Countable interface)
         */
        public function count(): int {}

        /**
         * Get the number of elements in the Series
         */
        public function len(): int {}

        /**
         * Check if Series is empty
         */
        public function isEmpty(): bool {}

        /**
         * Get the first n elements
         */
        public function head(int $n = 10): \Polars\Series {}

        /**
         * Get the last n elements
         */
        public function tail(int $n = 10): \Polars\Series {}

        /**
         * Get a single value from the Series (must have exactly one element)
         */
        public function item(): mixed {}

        /**
         * Get the first element
         */
        public function first(): mixed {}

        /**
         * Get the last element
         */
        public function last(): mixed {}

        /**
         * Extract a slice of the Series
         */
        public function slice(int $offset, int $length): \Polars\Series {}

        /**
         * Get the sum of all values
         */
        public function sum(): mixed {}

        /**
         * Get the mean of all values
         */
        public function mean(): float {}

        /**
         * Get the median of all values
         */
        public function median(): float {}

        /**
         * Get the minimum value
         */
        public function min(): mixed {}

        /**
         * Get the maximum value
         */
        public function max(): mixed {}

        /**
         * Get the standard deviation
         */
        public function std(int $ddof = 1): float {}

        /**
         * Get the variance
         */
        public function variance(int $ddof = 1): float {}

        /**
         * Get the product of all values
         */
        public function product(): mixed {}

        /**
         * Count non-null values
         */
        public function countNonNull(): int {}

        /**
         * Count null values
         */
        public function nullCount(): int {}

        /**
         * Count unique values
         */
        public function nUnique(): int {}

        /**
         * Check which values are null
         */
        public function isNull(): \Polars\Series {}

        /**
         * Check which values are not null
         */
        public function isNotNull(): \Polars\Series {}

        /**
         * Check which values are NaN
         */
        public function isNan(): \Polars\Series {}

        /**
         * Check which values are not NaN
         */
        public function isNotNan(): \Polars\Series {}

        /**
         * Check if any value is true (for boolean Series)
         */
        public function any(): bool {}

        /**
         * Check if all values are true (for boolean Series)
         */
        public function all(): bool {}

        /**
         * Element-wise equality comparison
         * @param int|float|string|bool|null $other
         */
        public function eq(mixed $other): \Polars\Series {}

        /**
         * Element-wise inequality comparison
         * @param int|float|string|bool|null $other
         */
        public function ne(mixed $other): \Polars\Series {}

        /**
         * Element-wise less than comparison
         * @param int|float|string|bool|null $other
         */
        public function lt(mixed $other): \Polars\Series {}

        /**
         * Element-wise less than or equal comparison
         * @param int|float|string|bool|null $other
         */
        public function le(mixed $other): \Polars\Series {}

        /**
         * Element-wise greater than comparison
         * @param int|float|string|bool|null $other
         */
        public function gt(mixed $other): \Polars\Series {}

        /**
         * Element-wise greater than or equal comparison
         * @param int|float|string|bool|null $other
         */
        public function ge(mixed $other): \Polars\Series {}

        /**
         * Sort the Series
         */
        public function sort(bool $descending = false, bool $nullsLast = true): \Polars\Series {}

        /**
         * Reverse the Series
         */
        public function reverse(): \Polars\Series {}

        /**
         * Get unique values
         */
        public function unique(): \Polars\Series {}

        /**
         * Remove null values
         */
        public function dropNulls(): \Polars\Series {}

        /**
         * Fill null values using forward strategy
         */
        public function fillNullForward(): \Polars\Series {}

        /**
         * Fill null values using backward strategy
         */
        public function fillNullBackward(): \Polars\Series {}

        /**
         * Fill null values with the mean
         */
        public function fillNullMean(): \Polars\Series {}

        /**
         * Fill null values with zero
         */
        public function fillNullZero(): \Polars\Series {}

        /**
         * Convert Series to PHP array
         */
        public function toArray(): array {}

        /**
         * Rename the Series
         */
        public function rename(string $name): \Polars\Series {}

        /**
         * Create an alias for the Series (same as rename)
         */
        public function alias(string $name): \Polars\Series {}

        /**
         * Create a copy of the Series
         */
        public function copy(): \Polars\Series {}

        /**
         * Cast Series to a different data type
         * @param string $dtype One of: 'int8', 'int16', 'int32', 'int64', 'uint8', 'uint16', 'uint32', 'uint64', 'float32', 'float64', 'bool', 'string'
         */
        public function cast(string $dtype): \Polars\Series {}

        /**
         * Display the Series
         */
        public function __toString(): string {}

        /**
         * Create a new Series from a PHP array
         *
         * # Example (PHP)
         * ```php
         * $s = new Series('values', [1, 2, 3, 4, 5]);
         * $s = new Series('names', ['Alice', 'Bob', 'Charlie']);
         * ```
         */
        public function __construct(string $name = "", array $values) {}
    }

    class Expr {
        public static function col(string $name): \Polars\Expr {}

        public static function cols(array $names): \Polars\Expr {}

        public static function all(): \Polars\Expr {}

        public function any(bool $ignoreNulls = true): \Polars\Expr {}

        public function count(): \Polars\Expr {}

        public function first(): \Polars\Expr {}

        public function last(): \Polars\Expr {}

        public function len(): \Polars\Expr {}

        public function max(): \Polars\Expr {}

        public function mean(): \Polars\Expr {}

        public function median(): \Polars\Expr {}

        public function min(): \Polars\Expr {}

        public function nUnique(): \Polars\Expr {}

        public function nanMax(): \Polars\Expr {}

        public function nanMin(): \Polars\Expr {}

        public function nullCount(): \Polars\Expr {}

        public function product(): \Polars\Expr {}

        public function std(int $ddof = 1): \Polars\Expr {}

        public function sum(): \Polars\Expr {}

        public function variance(int $ddof = 1): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function eq(mixed $other): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function eqMissing(mixed $other): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function ge(mixed $other): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function gt(mixed $other): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function le(mixed $other): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function lt(mixed $other): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function ne(mixed $other): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function neqMissing(mixed $other): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function add(mixed $other): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function floorDiv(mixed $other): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function modulo(mixed $other): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function mul(mixed $other): \Polars\Expr {}

        public function neg(): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function pow(mixed $other): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function sub(mixed $other): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function div(mixed $other): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function xxor(mixed $other): \Polars\Expr {}

        public function hasNulls(): \Polars\Expr {}

        /**
         * Set an alias for the expression
         */
        public function alias(string $name): \Polars\Expr {}

        /**
         * Shift values by n positions
         */
        public function shift(mixed $n): \Polars\Expr {}

        /**
         * Take every nth value
         */
        public function gatherEvery(int $n, int $offset = 0): \Polars\Expr {}

        /**
         * Cast to a data type
         */
        public function cast(string $dtype): \Polars\Expr {}

        public function isBetween(mixed $lowerBound, mixed $upperBound, \Polars\ClosedInterval $closed): \Polars\Expr {}

        /**
         * Constructor creates LiteralValue from int, float, string, boolean, or null. Passing other values will cause throwing exception
         * @throws Polars\Exception
         */
        public function __construct(mixed $value) {}
    }

    class DataType {
        public function __toString(): string {}

        public function __construct() {}
    }

    class LazyFrame {
        /**
         * Scan a CSV file into a LazyFrame
         */
        public static function scanCsv(string $path, bool $hasHeader = true, string $separator = ","): \Polars\LazyFrame {}

        /**
         * Scan a NDJSON file into a LazyFrame
         */
        public static function scanNdjson(string $path): \Polars\LazyFrame {}

        /**
         * Scan a Parquet file into a LazyFrame
         */
        public static function scanParquet(string $path): \Polars\LazyFrame {}

        /**
         * Execute the lazy query and return a DataFrame
         * @return \Polars\DataFrame
         */
        public function collect(): \Polars\DataFrame {}

        /**
         * Select columns by expression
         * @param \Polars\Expr[] $expressions
         * @return \Polars\LazyFrame
         */
        public function select(array $expressions): \Polars\LazyFrame {}

        /**
         * Filter rows by expression
         * @return \Polars\LazyFrame
         */
        public function filter(\Polars\Expr $expression): \Polars\LazyFrame {}

        /**
         * Add or modify columns
         * @param \Polars\Expr[] $expressions
         * @return \Polars\LazyFrame
         */
        public function withColumns(array $expressions): \Polars\LazyFrame {}

        /**
         * Group by expressions
         * @param \Polars\Expr[] $expressions
         * @return \Polars\LazyGroupBy
         */
        public function groupBy(array $expressions): \Polars\LazyGroupBy {}

        /**
         * Sort by a single column
         * @return \Polars\LazyFrame
         */
        public function sort(string $column, bool $descending = false, bool $nullsLast = true): \Polars\LazyFrame {}

        /**
         * Get column names
         * @return string[]
         */
        public function getColumns(): array {}

        /**
         * Get data types
         * @return \Polars\DataType[]
         */
        public function dtypes(): array {}

        /**
         * Get number of columns
         * @return int
         */
        public function width(): int {}

        /**
         * Get schema description as string
         * @return string
         */
        public function schema(): string {}

        /**
         * Get the first n rows
         * @return \Polars\LazyFrame
         */
        public function head(int $n = 10): \Polars\LazyFrame {}

        /**
         * Get the last n rows
         * @return \Polars\LazyFrame
         */
        public function tail(int $n = 10): \Polars\LazyFrame {}

        /**
         * Get the first row
         * @return \Polars\LazyFrame
         */
        public function first(): \Polars\LazyFrame {}

        /**
         * Get the last row
         * @return \Polars\LazyFrame
         */
        public function last(): \Polars\LazyFrame {}

        /**
         * Get a slice of rows
         * @return \Polars\LazyFrame
         */
        public function slice(int $offset, int $length): \Polars\LazyFrame {}

        /**
         * Limit to n rows (alias for head)
         * @return \Polars\LazyFrame
         */
        public function limit(int $n = 10): \Polars\LazyFrame {}

        /**
         * Return the number of non-null elements for each column
         * @return \Polars\LazyFrame
         */
        public function count(): \Polars\LazyFrame {}

        /**
         * Aggregate the columns to their sum value
         * @return \Polars\LazyFrame
         */
        public function sum(): \Polars\LazyFrame {}

        /**
         * Aggregate the columns to their mean value
         * @return \Polars\LazyFrame
         */
        public function mean(): \Polars\LazyFrame {}

        /**
         * Aggregate the columns to their median value
         * @return \Polars\LazyFrame
         */
        public function median(): \Polars\LazyFrame {}

        /**
         * Aggregate the columns to their minimum value
         * @return \Polars\LazyFrame
         */
        public function min(): \Polars\LazyFrame {}

        /**
         * Aggregate the columns to their maximum value
         * @return \Polars\LazyFrame
         */
        public function max(): \Polars\LazyFrame {}

        /**
         * Aggregate the columns to their standard deviation
         * @return \Polars\LazyFrame
         */
        public function std(int $ddof = 0): \Polars\LazyFrame {}

        /**
         * Aggregate the columns to their variance
         * @return \Polars\LazyFrame
         */
        public function variance(int $ddof = 0): \Polars\LazyFrame {}

        /**
         * Aggregate the columns to their quantile value
         * @return \Polars\LazyFrame
         */
        public function quantile(float $quantile): \Polars\LazyFrame {}

        /**
         * Aggregate the columns to their null count
         * @return \Polars\LazyFrame
         */
        public function nullCount(): \Polars\LazyFrame {}

        /**
         * Drop columns
         * @param string[] $columns
         * @return \Polars\LazyFrame
         */
        public function drop(array $columns): \Polars\LazyFrame {}

        /**
         * Rename columns
         * @param string[] $existing Old column names
         * @param string[] $newNames New column names
         * @return \Polars\LazyFrame
         */
        public function rename(array $existing, array $newNames): \Polars\LazyFrame {}

        /**
         * Get unique rows
         * @param string[]|null $subset Column names to consider for uniqueness
         * @return \Polars\LazyFrame
         */
        public function unique(?array $subset = null, string $keep = "first"): \Polars\LazyFrame {}

        /**
         * Drop rows with null values
         * @param string[]|null $subset Column names to check
         * @return \Polars\LazyFrame
         */
        public function dropNulls(?array $subset = null): \Polars\LazyFrame {}

        /**
         * Fill null values with a value or expression
         * @param int|float|string|bool|null|\Polars\Expr $value
         * @return \Polars\LazyFrame
         */
        public function fillNull(mixed $value): \Polars\LazyFrame {}

        /**
         * Fill NaN values with a value or expression
         * @param int|float|string|bool|null|\Polars\Expr $value
         * @return \Polars\LazyFrame
         */
        public function fillNan(mixed $value): \Polars\LazyFrame {}

        /**
         * Join with another LazyFrame
         * @param \Polars\LazyFrame $other The right LazyFrame
         * @param \Polars\Expr[] $on Join columns (used for both left and right)
         * @param string $how Join type: 'inner', 'left', 'right', 'full', 'cross'
         * @return \Polars\LazyFrame
         */
        public function join(\Polars\LazyFrame $other, array $on, string $how = "inner"): \Polars\LazyFrame {}

        /**
         * Add a row index column
         * @return \Polars\LazyFrame
         */
        public function withRowIndex(string $name = "index", int $offset = 0): \Polars\LazyFrame {}

        /**
         * Reverse row order
         * @return \Polars\LazyFrame
         */
        public function reverse(): \Polars\LazyFrame {}

        /**
         * Return the query plan as a string
         * @return string
         */
        public function explain(bool $optimized = true): string {}

        /**
         * Cache the LazyFrame computation
         * @return \Polars\LazyFrame
         */
        public function cache(): \Polars\LazyFrame {}

        /**
         * Sink the LazyFrame to a CSV file and return the result as a DataFrame
         * @return \Polars\DataFrame
         */
        public function sinkCsv(string $path, bool $includeHeader = true, string $separator = ","): \Polars\DataFrame {}

        /**
         * Sink the LazyFrame to a Parquet file and return the result as a DataFrame
         * @return \Polars\DataFrame
         */
        public function sinkParquet(string $path): \Polars\DataFrame {}

        /**
         * Sink the LazyFrame to a NDJSON file and return the result as a DataFrame
         * @return \Polars\DataFrame
         */
        public function sinkNdjson(string $path): \Polars\DataFrame {}

        /**
         * String representation showing the query plan
         */
        public function __toString(): string {}

        public function __construct() {}
    }

    class LazyGroupBy {
        /**
         * Aggregate using expressions
         * @param \Polars\Expr[] $expressions
         * @return \Polars\LazyFrame
         */
        public function agg(array $expressions): \Polars\LazyFrame {}

        /**
         * Count rows per group
         * @return \Polars\LazyFrame
         */
        public function count(): \Polars\LazyFrame {}

        /**
         * First row per group
         * @return \Polars\LazyFrame
         */
        public function first(): \Polars\LazyFrame {}

        /**
         * Last row per group
         * @return \Polars\LazyFrame
         */
        public function last(): \Polars\LazyFrame {}

        /**
         * First n rows per group
         * @return \Polars\LazyFrame
         */
        public function head(int $n = 5): \Polars\LazyFrame {}

        /**
         * Last n rows per group
         * @return \Polars\LazyFrame
         */
        public function tail(int $n = 5): \Polars\LazyFrame {}

        /**
         * Sum per group
         * @return \Polars\LazyFrame
         */
        public function sum(): \Polars\LazyFrame {}

        /**
         * Mean per group
         * @return \Polars\LazyFrame
         */
        public function mean(): \Polars\LazyFrame {}

        /**
         * Median per group
         * @return \Polars\LazyFrame
         */
        public function median(): \Polars\LazyFrame {}

        /**
         * Min per group
         * @return \Polars\LazyFrame
         */
        public function min(): \Polars\LazyFrame {}

        /**
         * Max per group
         * @return \Polars\LazyFrame
         */
        public function max(): \Polars\LazyFrame {}

        public function __construct() {}
    }

    enum ClosedInterval {
      case Both;
      case Left;
      case Right;
      case None;
    }
}
