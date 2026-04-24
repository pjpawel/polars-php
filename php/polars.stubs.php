<?php

// Stubs for polars-php

namespace Polars {
    enum ClosedInterval {
      case Both;
      case Left;
      case Right;
      case None;
    }

    class DataFrame implements \ArrayAccess {
        /**
         * Get columns names
         * @returns string[]
         *
         * @var array
         */
        public array $columns;

        /**
         * @return \Polars\DataType[]
         *
         * @var array
         */
        public readonly array $dtypes;

        /**
         * Get schema description as string
         *
         * @var string
         */
        public readonly string $schema;

        /**
         * Create a new DataFrame from a PHP array
         * keys are column name
         *
         * @param array $data
         * @param bool $byKeys
         */
        public function __construct(array $data, bool $byKeys = true) {}

        /**
         * Display the DataFrame (returns a formatted string)
         *
         * @return string
         */
        public function __toString(): string {}

        /**
         * Get the bottom k rows by a column
         *
         * @param int $k
         * @param string $by
         * @return \Polars\DataFrame
         */
        public function bottomK(int $k, string $by): \Polars\DataFrame {}

        /**
         * Cast columns to different data types
         * @param array $dtypes Associative array of column name => data type string
         *
         * @param array $dtypes
         * @param bool $strict
         * @return \Polars\DataFrame
         */
        public function cast(array $dtypes, bool $strict = false): \Polars\DataFrame {}

        /**
         * Create an empty copy of the DataFrame (same schema, no rows)
         *
         * @return \Polars\DataFrame
         */
        public function clear(): \Polars\DataFrame {}

        /**
         * Get a single column as a Series
         * @return \Polars\Series
         *
         * @param string $name
         * @return \Polars\Series
         */
        public function column(string $name): \Polars\Series {}

        /**
         * Get column names
         * @returns string[]
         *
         * @return array
         */
        public function columnNames(): array {}

        /**
         * Create a copy of the DataFrame
         *
         * @return \Polars\DataFrame
         */
        public function copy(): \Polars\DataFrame {}

        /**
         * @return \Polars\DataFrame Return the number of non-null elements for each column.
         *
         * @return \Polars\DataFrame
         */
        public function count(): \Polars\DataFrame {}

        /**
         * Get descriptive statistics (count, null_count, mean, std, min, max, median)
         *
         * @return \Polars\DataFrame
         */
        public function describe(): \Polars\DataFrame {}

        /**
         * Drop specified columns
         * @param string[] $columns
         *
         * @param array $columns
         * @return \Polars\DataFrame
         */
        public function drop(array $columns): \Polars\DataFrame {}

        /**
         * Remove a column and return it as a Series
         *
         * @param string $name
         * @return \Polars\Series
         */
        public function dropInPlace(string $name): \Polars\Series {}

        /**
         * Drop rows with NaN values
         * @param string[]|null $subset Column names to check
         *
         * @param array|null $subset
         * @return \Polars\DataFrame
         */
        public function dropNans(?array $subset = null): \Polars\DataFrame {}

        /**
         * Drop rows with null values
         * @param string[]|null $subset Column names to check
         *
         * @param array|null $subset
         * @return \Polars\DataFrame
         */
        public function dropNulls(?array $subset = null): \Polars\DataFrame {}

        /**
         * Check if two DataFrames are equal
         * @param \Polars\DataFrame $other
         *
         * @param \Polars\DataFrame $other
         * @return bool
         */
        public function equals(\Polars\DataFrame $other): bool {}

        /**
         * Get the estimated size in bytes
         *
         * @return int
         */
        public function estimatedSize(): int {}

        /**
         * Explode list columns into rows
         * @param string[] $columns Column names to explode
         *
         * @param array $columns
         * @return \Polars\DataFrame
         */
        public function explode(array $columns): \Polars\DataFrame {}

        /**
         * Extend this DataFrame with rows from another DataFrame (in-place)
         *
         * @param \Polars\DataFrame $other
         * @return void
         */
        public function extend(\Polars\DataFrame $other): void {}

        /**
         * Fill NaN values with a value or expression
         * @param int|float|string|bool|null|\Polars\Expr $value
         *
         * @param mixed $value
         * @return \Polars\DataFrame
         */
        public function fillNan(mixed $value): \Polars\DataFrame {}

        /**
         * Fill null values with a value or expression
         * @param int|float|string|bool|null|\Polars\Expr $value
         *
         * @param mixed $value
         * @return \Polars\DataFrame
         */
        public function fillNull(mixed $value): \Polars\DataFrame {}

        /**
         * Filter rows by expression
         * @param \Polars\Expr $expression
         *
         * @param \Polars\Expr $expression
         * @return \Polars\DataFrame
         */
        public function filter(\Polars\Expr $expression): \Polars\DataFrame {}

        /**
         * Take every nth row
         *
         * @param int $n
         * @param int $offset
         * @return \Polars\DataFrame
         */
        public function gatherEvery(int $n, int $offset = 0): \Polars\DataFrame {}

        /**
         * Get the column index by name, returns -1 if not found
         *
         * @param string $name
         * @return int
         */
        public function getColumnIndex(string $name): int {}

        /**
         * Get all columns as an array of Series
         * @return \Polars\Series[]
         *
         * @return array
         */
        public function getSeries(): array {}

        /**
         * Get a quick summary of the DataFrame
         *
         * @return string
         */
        public function glimpse(): string {}

        /**
         * Group by expressions
         * @param \Polars\Expr[] $expressions
         * @return \Polars\LazyGroupBy
         *
         * @param array $expressions
         * @return \Polars\LazyGroupBy
         */
        public function groupBy(array $expressions): \Polars\LazyGroupBy {}

        /**
         * Get the first n rows
         *
         * @param int $n
         * @return \Polars\DataFrame
         */
        public function head(int $n = 10): \Polars\DataFrame {}

        /**
         * @return int Get the number of rows
         *
         * @return int
         */
        public function height(): int {}

        /**
         * Grow this DataFrame horizontally by adding Series columns
         * @param \Polars\Series[] $columns
         *
         * @param array $columns
         * @return \Polars\DataFrame
         */
        public function hstack(array $columns): \Polars\DataFrame {}

        /**
         * Insert a column at a given index
         *
         * @param int $index
         * @param \Polars\Series $series
         * @return void
         */
        public function insertColumn(int $index, \Polars\Series $series): void {}

        /**
         * Interpolate null values using linear interpolation
         *
         * @return \Polars\DataFrame
         */
        public function interpolate(): \Polars\DataFrame {}

        /**
         * Get a boolean mask of duplicated rows
         * @return \Polars\Series
         *
         * @return \Polars\Series
         */
        public function isDuplicated(): \Polars\Series {}

        /**
         * Check if DataFrame is empty
         *
         * @return bool
         */
        public function isEmpty(): bool {}

        /**
         * Get a boolean mask of unique rows
         * @return \Polars\Series
         *
         * @return \Polars\Series
         */
        public function isUnique(): \Polars\Series {}

        /**
         * Return the DataFrame as a scalar value
         * The DataFrame must contain exactly one element (1 row, 1 column)
         *
         * @return mixed
         */
        public function item(): mixed {}

        /**
         * Join with another DataFrame
         * @param \Polars\DataFrame $other The right DataFrame
         * @param \Polars\Expr[] $on Join columns (used for both left and right when leftOn/rightOn not given)
         * @param string $how Join type: 'inner', 'left', 'right', 'full', 'cross'
         * @param \Polars\Expr[]|null $leftOn Left join columns (overrides $on for the left side)
         * @param \Polars\Expr[]|null $rightOn Right join columns (overrides $on for the right side)
         * @param string|null $suffix Suffix for duplicate column names (default: '_right')
         * @param string|null $validate Join validation: 'm:m', 'm:1', '1:m', '1:1'
         * @param bool|null $coalesce Coalesce join columns
         *
         * @param \Polars\DataFrame $other
         * @param array $on
         * @param string $how
         * @param array|null $leftOn
         * @param array|null $rightOn
         * @param string|null $suffix
         * @param string|null $validate
         * @param bool|null $coalesce
         * @return \Polars\DataFrame
         */
        public function join(\Polars\DataFrame $other, array $on, string $how = "inner", ?array $leftOn = null, ?array $rightOn = null, ?string $suffix = null, ?string $validate = null, ?bool $coalesce = null): \Polars\DataFrame {}

        /**
         * Perform an asof join with another DataFrame
         * @param \Polars\DataFrame $other The right DataFrame
         * @param string $on Column to join on (must be sorted)
         * @param string|null $leftBy Group by column for left DataFrame
         * @param string|null $rightBy Group by column for right DataFrame
         * @param string|null $tolerance Tolerance for the asof join (time duration string e.g. "5m")
         * @param string $strategy Join strategy: 'backward', 'forward', 'nearest'
         *
         * @param \Polars\DataFrame $other
         * @param string $on
         * @param string|null $strategy
         * @param string|null $leftBy
         * @param string|null $rightBy
         * @param string|null $tolerance
         * @return \Polars\DataFrame
         */
        public function joinAsof(\Polars\DataFrame $other, string $on, ?string $strategy = null, ?string $leftBy = null, ?string $rightBy = null, ?string $tolerance = null): \Polars\DataFrame {}

        /**
         * Join with another DataFrame using arbitrary predicates
         * @param \Polars\DataFrame $other The right DataFrame
         * @param \Polars\Expr[] $predicates Join predicates
         *
         * @param \Polars\DataFrame $other
         * @param array $predicates
         * @return \Polars\DataFrame
         */
        public function joinWhere(\Polars\DataFrame $other, array $predicates): \Polars\DataFrame {}

        /**
         * Convert this DataFrame to a LazyFrame for lazy evaluation
         *
         * @return \Polars\LazyFrame
         */
        public function lazy(): \Polars\LazyFrame {}

        /**
         * Limit to n rows (alias for head)
         *
         * @param int $n
         * @return \Polars\DataFrame
         */
        public function limit(int $n = 10): \Polars\DataFrame {}

        /**
         * Aggregate the columns of this DataFrame to their maximum value.
         *
         * @return \Polars\DataFrame
         */
        public function max(): \Polars\DataFrame {}

        /**
         * Aggregate the columns of this DataFrame to their mean value.
         *
         * @return \Polars\DataFrame
         */
        public function mean(): \Polars\DataFrame {}

        /**
         * Aggregate the columns to their median value.
         *
         * @return \Polars\DataFrame
         */
        public function median(): \Polars\DataFrame {}

        /**
         * Unpivot (alias for unpivot, deprecated name)
         *
         * @param array $on
         * @param array $index
         * @param string|null $variableName
         * @param string|null $valueName
         * @return \Polars\DataFrame
         */
        public function melt(array $on, array $index, ?string $variableName = null, ?string $valueName = null): \Polars\DataFrame {}

        /**
         * Merge two sorted DataFrames by a key column
         * @param \Polars\DataFrame $other The other sorted DataFrame
         * @param string $key The column to merge on (must be sorted in both DataFrames)
         *
         * @param \Polars\DataFrame $other
         * @param string $key
         * @return \Polars\DataFrame
         */
        public function mergeSorted(\Polars\DataFrame $other, string $key): \Polars\DataFrame {}

        /**
         * Aggregate the columns of this DataFrame to their minimal value.
         *
         * @return \Polars\DataFrame
         */
        public function min(): \Polars\DataFrame {}

        /**
         * Get the number of unique values per column
         *
         * @return \Polars\DataFrame
         */
        public function nUnique(): \Polars\DataFrame {}

        /**
         * Aggregate the columns to their null count.
         *
         * @return \Polars\DataFrame
         */
        public function nullCount(): \Polars\DataFrame {}

        /**
         * Check if an offset (column name) exists
         *
         * @param mixed $offset
         * @return bool
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
         *
         * @param mixed $offset
         * @return \Polars\DataFrame
         */
        public function offsetGet(mixed $offset): \Polars\DataFrame {}

        /**
         * Set value at offset - not supported for DataFrames
         * @return void
         *
         * @param mixed $_offset
         * @param mixed $_value
         * @return void
         */
        public function offsetSet(mixed $_offset, mixed $_value): void {}

        /**
         * Unset value at offset - not supported for DataFrames
         * @return void
         *
         * @param mixed $_offset
         * @return void
         */
        public function offsetUnset(mixed $_offset): void {}

        /**
         * Split DataFrame into multiple DataFrames based on unique values in given columns
         * @param string[] $by Column names to partition by
         * @param bool $maintainOrder Maintain the order of the original DataFrame
         * @param bool $includeKey Include the partition key columns in each partition
         *
         * @param array $by
         * @param bool $maintainOrder
         * @param bool $includeKey
         * @return array
         */
        public function partitionBy(array $by, bool $maintainOrder = true, bool $includeKey = true): array {}

        /**
         * Pivot a DataFrame from long to wide format
         * @param string[] $on Column(s) to use for the pivot
         * @param string[]|null $index Column(s) to use as row index
         * @param string[]|null $values Column(s) to aggregate
         * @param string|null $aggregateFunction Aggregation function: 'first', 'last', 'sum', 'mean', 'median', 'min', 'max', 'count', 'len'
         * @param bool $sortColumns Sort the resulting pivot columns
         *
         * @param array $on
         * @param array|null $index
         * @param array|null $values
         * @param string|null $aggregateFunction
         * @param bool $sortColumns
         * @return \Polars\DataFrame
         */
        public function pivot(array $on, ?array $index = null, ?array $values = null, ?string $aggregateFunction = null, bool $sortColumns = false): \Polars\DataFrame {}

        /**
         * Aggregate the columns to their product value.
         *
         * @return \Polars\DataFrame
         */
        public function product(): \Polars\DataFrame {}

        /**
         * Aggregate the columns to their quantile value.
         *
         * @param float $quantile
         * @return \Polars\DataFrame
         */
        public function quantile(float $quantile): \Polars\DataFrame {}

        /**
         * Read a DataFrame from a CSV file
         *
         * @param string $path
         * @param bool $hasHeader
         * @param string $separator
         * @return \Polars\DataFrame
         */
        public static function readCsv(string $path, bool $hasHeader = true, string $separator = ","): \Polars\DataFrame {}

        /**
         * Read a DataFrame from a JSON file
         *
         * @param string $path
         * @return \Polars\DataFrame
         */
        public static function readJson(string $path): \Polars\DataFrame {}

        /**
         * Read a DataFrame from a NDJSON (newline-delimited JSON) file
         *
         * @param string $path
         * @return \Polars\DataFrame
         */
        public static function readNdjson(string $path): \Polars\DataFrame {}

        /**
         * Read a DataFrame from a Parquet file
         *
         * @param string $path
         * @return \Polars\DataFrame
         */
        public static function readParquet(string $path): \Polars\DataFrame {}

        /**
         * Rechunk the DataFrame into contiguous memory
         *
         * @return \Polars\DataFrame
         */
        public function rechunk(): \Polars\DataFrame {}

        /**
         * Remove a row at the given index
         *
         * @param int $index
         * @return \Polars\DataFrame
         */
        public function remove(int $index): \Polars\DataFrame {}

        /**
         * Rename columns
         * @param string[] $existing Old column names
         * @param string[] $newNames New column names
         *
         * @param array $existing
         * @param array $newNames
         * @return \Polars\DataFrame
         */
        public function rename(array $existing, array $newNames): \Polars\DataFrame {}

        /**
         * Replace a column at a given index
         *
         * @param int $index
         * @param \Polars\Series $series
         * @return void
         */
        public function replaceColumn(int $index, \Polars\Series $series): void {}

        /**
         * Reverse row order
         *
         * @return \Polars\DataFrame
         */
        public function reverse(): \Polars\DataFrame {}

        /**
         * Get a single row as an associative array (supports negative indexing)
         *
         * @param int $index
         * @return array
         */
        public function row(int $index): array {}

        /**
         * Get all rows as array of associative arrays (alias for toArray)
         *
         * @return array
         */
        public function rows(): array {}

        /**
         * Randomly sample rows by count or fraction
         * @param int $n Number of rows to sample (ignored if $fraction is set)
         * @param bool $withReplacement Allow sampling with replacement
         * @param bool $shuffle Shuffle the sampled rows
         * @param float|null $fraction Fraction of rows to sample (0.0 to 1.0), overrides $n
         * @param int|null $seed Random seed for reproducibility
         *
         * @param int $n
         * @param bool $withReplacement
         * @param bool $shuffle
         * @param float|null $fraction
         * @param int|null $seed
         * @return \Polars\DataFrame
         */
        public function sample(int $n = 0, bool $withReplacement = false, bool $shuffle = true, ?float $fraction = null, ?int $seed = null): \Polars\DataFrame {}

        /**
         * Make select based on given expressions
         * @param \Polars\Expr[]
         *
         * @param array $expressions
         * @return \Polars\DataFrame
         */
        public function select(array $expressions): \Polars\DataFrame {}

        /**
         * Select columns sequentially (no parallel execution)
         *
         * @param array $expressions
         * @return \Polars\DataFrame
         */
        public function selectSeq(array $expressions): \Polars\DataFrame {}

        /**
         * Set the sorted flag on a column
         *
         * @param string $column
         * @param bool $descending
         * @return void
         */
        public function setSorted(string $column, bool $descending = false): void {}

        /**
         * @return int[] Get the shape of the DataFrame as [rows, columns]
         *
         * @return array
         */
        public function shape(): array {}

        /**
         * Shift column values by n positions
         *
         * @param int $n
         * @return \Polars\DataFrame
         */
        public function shift(int $n): \Polars\DataFrame {}

        /**
         * Shrink memory usage of the DataFrame
         * @return void
         *
         * @return void
         */
        public function shrinkToFit(): void {}

        /**
         * Get a slice of rows
         *
         * @param int $offset
         * @param int $length
         * @return \Polars\DataFrame
         */
        public function slice(int $offset, int $length): \Polars\DataFrame {}

        /**
         * Sort DataFrame by one or more columns
         * @param string|string[] $by Column name or array of column names to sort by
         * @param bool $descending Sort order (applies to all columns)
         * @param bool $nullsLast Put null values last
         * @param bool $maintainOrder Maintain order of equal elements (stable sort)
         * @param bool $multithreaded Use multithreaded sorting
         *
         * @param mixed $by
         * @param bool $descending
         * @param bool $nullsLast
         * @param bool $maintainOrder
         * @param bool $multithreaded
         * @return \Polars\DataFrame
         */
        public function sort(mixed $by, bool $descending = false, bool $nullsLast = true, bool $maintainOrder = false, bool $multithreaded = true): \Polars\DataFrame {}

        /**
         * Execute a SQL query against this DataFrame
         * The DataFrame is registered as table named "self"
         * @param string $query SQL query string
         *
         * @param string $query
         * @return \Polars\DataFrame
         */
        public function sql(string $query): \Polars\DataFrame {}

        /**
         * Aggregate the columns of this DataFrame to their product value.
         *
         * @param int $ddof
         * @return \Polars\DataFrame
         */
        public function std(int $ddof = 0): \Polars\DataFrame {}

        /**
         * Aggregate the columns to their sum value.
         *
         * @return \Polars\DataFrame
         */
        public function sum(): \Polars\DataFrame {}

        /**
         * Get the last n rows
         *
         * @param int $n
         * @return \Polars\DataFrame
         */
        public function tail(int $n = 10): \Polars\DataFrame {}

        /**
         * Convert DataFrame to a PHP array of associative arrays (rows)
         *
         * @return array
         */
        public function toArray(): array {}

        /**
         * Convert columns to one-hot encoded (dummy) variables
         * @param string[]|null $columns Columns to encode (null = all)
         * @param string $separator Separator between column name and value
         * @param bool $dropFirst Drop the first category to avoid multicollinearity
         *
         * @param array|null $columns
         * @param string $separator
         * @param bool $dropFirst
         * @return \Polars\DataFrame
         */
        public function toDummies(?array $columns = null, string $separator = "_", bool $dropFirst = false): \Polars\DataFrame {}

        /**
         * Convert a single-column DataFrame to a Series
         *
         * @return \Polars\Series
         */
        public function toSeries(): \Polars\Series {}

        /**
         * Get the top k rows by a column
         *
         * @param int $k
         * @param string $by
         * @return \Polars\DataFrame
         */
        public function topK(int $k, string $by): \Polars\DataFrame {}

        /**
         * Transpose the DataFrame
         * @param bool $includeHeader Include column names as first column
         * @param string $headerName Name for the header column
         * @param string[]|null $columnNames Custom names for the transposed columns
         *
         * @param bool $includeHeader
         * @param string $headerName
         * @param array|null $columnNames
         * @return \Polars\DataFrame
         */
        public function transpose(bool $includeHeader = false, string $headerName = "column", ?array $columnNames = null): \Polars\DataFrame {}

        /**
         * Get unique rows
         * @param string[]|null $subset Column names to consider for uniqueness
         *
         * @param array|null $subset
         * @param string $keep
         * @return \Polars\DataFrame
         */
        public function unique(?array $subset = null, string $keep = "first"): \Polars\DataFrame {}

        /**
         * Unnest struct columns into separate columns
         * @param string[] $columns Names of struct columns to unnest
         *
         * @param array $columns
         * @return \Polars\DataFrame
         */
        public function unnest(array $columns): \Polars\DataFrame {}

        /**
         * Unpivot a DataFrame from wide to long format
         * @param string[] $on Columns to use as values
         * @param string[] $index Columns to use as identifier
         * @param string|null $variableName Custom name for the variable column (default: 'variable')
         * @param string|null $valueName Custom name for the value column (default: 'value')
         *
         * @param array $on
         * @param array $index
         * @param string|null $variableName
         * @param string|null $valueName
         * @return \Polars\DataFrame
         */
        public function unpivot(array $on, array $index, ?string $variableName = null, ?string $valueName = null): \Polars\DataFrame {}

        /**
         * Aggregate the columns to their variance value.
         *
         * @param int $ddof
         * @return \Polars\DataFrame
         */
        public function variance(int $ddof = 0): \Polars\DataFrame {}

        /**
         * Grow this DataFrame vertically by stacking another DataFrame
         * @param \Polars\DataFrame $other
         *
         * @param \Polars\DataFrame $other
         * @return \Polars\DataFrame
         */
        public function vstack(\Polars\DataFrame $other): \Polars\DataFrame {}

        /**
         * @return int Get the number of columns
         *
         * @return int
         */
        public function width(): int {}

        /**
         * Add or modify columns
         * @param \Polars\Expr[] $expressions
         *
         * @param array $expressions
         * @return \Polars\DataFrame
         */
        public function withColumns(array $expressions): \Polars\DataFrame {}

        /**
         * Add or overwrite columns sequentially (no parallel execution)
         *
         * @param array $expressions
         * @return \Polars\DataFrame
         */
        public function withColumnsSeq(array $expressions): \Polars\DataFrame {}

        /**
         * Add a row count column (deprecated alias for withRowIndex)
         *
         * @param string $name
         * @param int $offset
         * @return \Polars\DataFrame
         */
        public function withRowCount(string $name = "row_nr", int $offset = 0): \Polars\DataFrame {}

        /**
         * Add a row index column
         *
         * @param string $name
         * @param int $offset
         * @return \Polars\DataFrame
         */
        public function withRowIndex(string $name = "index", int $offset = 0): \Polars\DataFrame {}

        /**
         * Write to CSV file
         *
         * @param string $path
         * @param bool $includeHeader
         * @param string $separator
         * @return void
         */
        public function writeCsv(string $path, bool $includeHeader, string $separator = ","): void {}

        /**
         * Write DataFrame to a JSON file
         *
         * @param string $path
         * @return void
         */
        public function writeJson(string $path): void {}

        /**
         * Write DataFrame to a NDJSON (newline-delimited JSON) file
         *
         * @param string $path
         * @return void
         */
        public function writeNdjson(string $path): void {}

        /**
         * Write DataFrame to a Parquet file
         *
         * @param string $path
         * @return void
         */
        public function writeParquet(string $path): void {}
    }

    class DataType {
        public function __construct() {}

        /**
         * @return string
         */
        public function __toString(): string {}
    }

    class Exception extends \Exception {
        public function __construct() {}
    }

    class Expr {
        /**
         * Constructor creates LiteralValue from int, float, string, boolean, or null. Passing other values will cause throwing exception
         * @throws Polars\Exception
         *
         * @param mixed $value
         */
        public function __construct(mixed $value) {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         *
         * @param mixed $other
         * @return \Polars\Expr
         */
        public function add(mixed $other): \Polars\Expr {}

        /**
         * Set an alias for the expression
         *
         * @param string $name
         * @return \Polars\Expr
         */
        public function alias(string $name): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public static function all(): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         *
         * @param mixed $other
         * @return \Polars\Expr
         */
        public function and_(mixed $other): \Polars\Expr {}

        /**
         * @param bool $ignoreNulls
         * @return \Polars\Expr
         */
        public function any(bool $ignoreNulls = true): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function approxNUnique(): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function argMax(): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function argMin(): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function bitwiseAnd(): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function bitwiseOr(): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function bitwiseXor(): \Polars\Expr {}

        /**
         * Cast to a data type
         *
         * @param string $dtype
         * @return \Polars\Expr
         */
        public function cast(string $dtype): \Polars\Expr {}

        /**
         * @param string $name
         * @return \Polars\Expr
         */
        public static function col(string $name): \Polars\Expr {}

        /**
         * @param array $names
         * @return \Polars\Expr
         */
        public static function cols(array $names): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function count(): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         *
         * @param mixed $other
         * @return \Polars\Expr
         */
        public function div(mixed $other): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         *
         * @param mixed $other
         * @return \Polars\Expr
         */
        public function eq(mixed $other): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         *
         * @param mixed $other
         * @return \Polars\Expr
         */
        public function eqMissing(mixed $other): \Polars\Expr {}

        /**
         * Exclude specific columns from the expression
         *
         * @param mixed $columns
         * @return \Polars\Expr
         */
        public function exclude(mixed $columns): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function first(): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         *
         * @param mixed $other
         * @return \Polars\Expr
         */
        public function floorDiv(mixed $other): \Polars\Expr {}

        /**
         * Take every nth value
         *
         * @param int $n
         * @param int $offset
         * @return \Polars\Expr
         */
        public function gatherEvery(int $n, int $offset = 0): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         *
         * @param mixed $other
         * @return \Polars\Expr
         */
        public function ge(mixed $other): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         *
         * @param mixed $other
         * @return \Polars\Expr
         */
        public function gt(mixed $other): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function hasNulls(): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function implode(): \Polars\Expr {}

        /**
         * @param mixed $lowerBound
         * @param mixed $upperBound
         * @param \Polars\ClosedInterval $closed
         * @return \Polars\Expr
         */
        public function isBetween(mixed $lowerBound, mixed $upperBound, \Polars\ClosedInterval $closed): \Polars\Expr {}

        /**
         * @param bool $fisher
         * @param bool $bias
         * @return \Polars\Expr
         */
        public function kurtosis(bool $fisher = true, bool $bias = true): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function last(): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         *
         * @param mixed $other
         * @return \Polars\Expr
         */
        public function le(mixed $other): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function len(): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         *
         * @param mixed $other
         * @return \Polars\Expr
         */
        public function lt(mixed $other): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function max(): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function mean(): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function median(): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function min(): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function mode(): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         *
         * @param mixed $other
         * @return \Polars\Expr
         */
        public function modulo(mixed $other): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         *
         * @param mixed $other
         * @return \Polars\Expr
         */
        public function mul(mixed $other): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function nUnique(): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function nanMax(): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function nanMin(): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         *
         * @param mixed $other
         * @return \Polars\Expr
         */
        public function ne(mixed $other): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function neg(): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         *
         * @param mixed $other
         * @return \Polars\Expr
         */
        public function neqMissing(mixed $other): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function nullCount(): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         *
         * @param mixed $other
         * @return \Polars\Expr
         */
        public function or_(mixed $other): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         *
         * @param mixed $other
         * @return \Polars\Expr
         */
        public function pow(mixed $other): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function product(): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $quantile Quantile value (0.0 to 1.0)
         * @throws Polars\Exception
         *
         * @param mixed $quantile
         * @param \Polars\QuantileMethod $interpolation
         * @return \Polars\Expr
         */
        public function quantile(mixed $quantile, \Polars\QuantileMethod $interpolation): \Polars\Expr {}

        /**
         * Shift values by n positions
         *
         * @param mixed $n
         * @return \Polars\Expr
         */
        public function shift(mixed $n): \Polars\Expr {}

        /**
         * @param bool $bias
         * @return \Polars\Expr
         */
        public function skew(bool $bias = true): \Polars\Expr {}

        /**
         * @param int $ddof
         * @return \Polars\Expr
         */
        public function std(int $ddof = 1): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         *
         * @param mixed $other
         * @return \Polars\Expr
         */
        public function sub(mixed $other): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function sum(): \Polars\Expr {}

        /**
         * @return \Polars\Expr
         */
        public function uniqueCounts(): \Polars\Expr {}

        /**
         * @param int $ddof
         * @return \Polars\Expr
         */
        public function variance(int $ddof = 1): \Polars\Expr {}

        /**
         * @param int|float|string|bool|null|\Polars\Expr $other Accepts numeric, string, bool, null or PolarsExpr object
         *
         * @param mixed $other
         * @return \Polars\Expr
         */
        public function xxor(mixed $other): \Polars\Expr {}
    }

    class LazyFrame {
        public function __construct() {}

        /**
         * String representation showing the query plan
         *
         * @return string
         */
        public function __toString(): string {}

        /**
         * Cache the LazyFrame computation
         * @return \Polars\LazyFrame
         *
         * @return \Polars\LazyFrame
         */
        public function cache(): \Polars\LazyFrame {}

        /**
         * Execute the lazy query and return a DataFrame
         * @return \Polars\DataFrame
         *
         * @return \Polars\DataFrame
         */
        public function collect(): \Polars\DataFrame {}

        /**
         * Return the number of non-null elements for each column
         * @return \Polars\LazyFrame
         *
         * @return \Polars\LazyFrame
         */
        public function count(): \Polars\LazyFrame {}

        /**
         * Drop columns
         * @param string[] $columns
         * @return \Polars\LazyFrame
         *
         * @param array $columns
         * @return \Polars\LazyFrame
         */
        public function drop(array $columns): \Polars\LazyFrame {}

        /**
         * Drop rows with null values
         * @param string[]|null $subset Column names to check
         * @return \Polars\LazyFrame
         *
         * @param array|null $subset
         * @return \Polars\LazyFrame
         */
        public function dropNulls(?array $subset = null): \Polars\LazyFrame {}

        /**
         * Get data types
         * @return \Polars\DataType[]
         *
         * @return array
         */
        public function dtypes(): array {}

        /**
         * Return the query plan as a string
         * @return string
         *
         * @param bool $optimized
         * @return string
         */
        public function explain(bool $optimized = true): string {}

        /**
         * Fill NaN values with a value or expression
         * @param int|float|string|bool|null|\Polars\Expr $value
         * @return \Polars\LazyFrame
         *
         * @param mixed $value
         * @return \Polars\LazyFrame
         */
        public function fillNan(mixed $value): \Polars\LazyFrame {}

        /**
         * Fill null values with a value or expression
         * @param int|float|string|bool|null|\Polars\Expr $value
         * @return \Polars\LazyFrame
         *
         * @param mixed $value
         * @return \Polars\LazyFrame
         */
        public function fillNull(mixed $value): \Polars\LazyFrame {}

        /**
         * Filter rows by expression
         * @return \Polars\LazyFrame
         *
         * @param \Polars\Expr $expression
         * @return \Polars\LazyFrame
         */
        public function filter(\Polars\Expr $expression): \Polars\LazyFrame {}

        /**
         * Get the first row
         * @return \Polars\LazyFrame
         *
         * @return \Polars\LazyFrame
         */
        public function first(): \Polars\LazyFrame {}

        /**
         * Get column names
         * @return string[]
         *
         * @return array
         */
        public function getColumns(): array {}

        /**
         * Group by expressions
         * @param \Polars\Expr[] $expressions
         * @return \Polars\LazyGroupBy
         *
         * @param array $expressions
         * @return \Polars\LazyGroupBy
         */
        public function groupBy(array $expressions): \Polars\LazyGroupBy {}

        /**
         * Get the first n rows
         * @return \Polars\LazyFrame
         *
         * @param int $n
         * @return \Polars\LazyFrame
         */
        public function head(int $n = 10): \Polars\LazyFrame {}

        /**
         * Join with another LazyFrame
         * @param \Polars\LazyFrame $other The right LazyFrame
         * @param \Polars\Expr[] $on Join columns (used for both left and right)
         * @param string $how Join type: 'inner', 'left', 'right', 'full', 'cross'
         * @return \Polars\LazyFrame
         *
         * @param \Polars\LazyFrame $other
         * @param array $on
         * @param string $how
         * @return \Polars\LazyFrame
         */
        public function join(\Polars\LazyFrame $other, array $on, string $how = "inner"): \Polars\LazyFrame {}

        /**
         * Get the last row
         * @return \Polars\LazyFrame
         *
         * @return \Polars\LazyFrame
         */
        public function last(): \Polars\LazyFrame {}

        /**
         * Limit to n rows (alias for head)
         * @return \Polars\LazyFrame
         *
         * @param int $n
         * @return \Polars\LazyFrame
         */
        public function limit(int $n = 10): \Polars\LazyFrame {}

        /**
         * Aggregate the columns to their maximum value
         * @return \Polars\LazyFrame
         *
         * @return \Polars\LazyFrame
         */
        public function max(): \Polars\LazyFrame {}

        /**
         * Aggregate the columns to their mean value
         * @return \Polars\LazyFrame
         *
         * @return \Polars\LazyFrame
         */
        public function mean(): \Polars\LazyFrame {}

        /**
         * Aggregate the columns to their median value
         * @return \Polars\LazyFrame
         *
         * @return \Polars\LazyFrame
         */
        public function median(): \Polars\LazyFrame {}

        /**
         * Aggregate the columns to their minimum value
         * @return \Polars\LazyFrame
         *
         * @return \Polars\LazyFrame
         */
        public function min(): \Polars\LazyFrame {}

        /**
         * Aggregate the columns to their null count
         * @return \Polars\LazyFrame
         *
         * @return \Polars\LazyFrame
         */
        public function nullCount(): \Polars\LazyFrame {}

        /**
         * Aggregate the columns to their quantile value
         * @return \Polars\LazyFrame
         *
         * @param float $quantile
         * @return \Polars\LazyFrame
         */
        public function quantile(float $quantile): \Polars\LazyFrame {}

        /**
         * Rename columns
         * @param string[] $existing Old column names
         * @param string[] $newNames New column names
         * @return \Polars\LazyFrame
         *
         * @param array $existing
         * @param array $newNames
         * @return \Polars\LazyFrame
         */
        public function rename(array $existing, array $newNames): \Polars\LazyFrame {}

        /**
         * Reverse row order
         * @return \Polars\LazyFrame
         *
         * @return \Polars\LazyFrame
         */
        public function reverse(): \Polars\LazyFrame {}

        /**
         * Scan a CSV file into a LazyFrame
         *
         * @param string $path
         * @param bool $hasHeader
         * @param string $separator
         * @return \Polars\LazyFrame
         */
        public static function scanCsv(string $path, bool $hasHeader = true, string $separator = ","): \Polars\LazyFrame {}

        /**
         * Scan a NDJSON file into a LazyFrame
         *
         * @param string $path
         * @return \Polars\LazyFrame
         */
        public static function scanNdjson(string $path): \Polars\LazyFrame {}

        /**
         * Scan a Parquet file into a LazyFrame
         *
         * @param string $path
         * @return \Polars\LazyFrame
         */
        public static function scanParquet(string $path): \Polars\LazyFrame {}

        /**
         * Get schema description as string
         * @return string
         *
         * @return string
         */
        public function schema(): string {}

        /**
         * Select columns by expression
         * @param \Polars\Expr[] $expressions
         * @return \Polars\LazyFrame
         *
         * @param array $expressions
         * @return \Polars\LazyFrame
         */
        public function select(array $expressions): \Polars\LazyFrame {}

        /**
         * Sink the LazyFrame to a CSV file and return the result as a DataFrame
         * @return \Polars\DataFrame
         *
         * @param string $path
         * @param bool $includeHeader
         * @param string $separator
         * @return \Polars\DataFrame
         */
        public function sinkCsv(string $path, bool $includeHeader = true, string $separator = ","): \Polars\DataFrame {}

        /**
         * Sink the LazyFrame to a NDJSON file and return the result as a DataFrame
         * @return \Polars\DataFrame
         *
         * @param string $path
         * @return \Polars\DataFrame
         */
        public function sinkNdjson(string $path): \Polars\DataFrame {}

        /**
         * Sink the LazyFrame to a Parquet file and return the result as a DataFrame
         * @return \Polars\DataFrame
         *
         * @param string $path
         * @return \Polars\DataFrame
         */
        public function sinkParquet(string $path): \Polars\DataFrame {}

        /**
         * Get a slice of rows
         * @return \Polars\LazyFrame
         *
         * @param int $offset
         * @param int $length
         * @return \Polars\LazyFrame
         */
        public function slice(int $offset, int $length): \Polars\LazyFrame {}

        /**
         * Sort by a single column
         * @return \Polars\LazyFrame
         *
         * @param string $column
         * @param bool $descending
         * @param bool $nullsLast
         * @return \Polars\LazyFrame
         */
        public function sort(string $column, bool $descending = false, bool $nullsLast = true): \Polars\LazyFrame {}

        /**
         * Aggregate the columns to their standard deviation
         * @return \Polars\LazyFrame
         *
         * @param int $ddof
         * @return \Polars\LazyFrame
         */
        public function std(int $ddof = 0): \Polars\LazyFrame {}

        /**
         * Aggregate the columns to their sum value
         * @return \Polars\LazyFrame
         *
         * @return \Polars\LazyFrame
         */
        public function sum(): \Polars\LazyFrame {}

        /**
         * Get the last n rows
         * @return \Polars\LazyFrame
         *
         * @param int $n
         * @return \Polars\LazyFrame
         */
        public function tail(int $n = 10): \Polars\LazyFrame {}

        /**
         * Get unique rows
         * @param string[]|null $subset Column names to consider for uniqueness
         * @return \Polars\LazyFrame
         *
         * @param array|null $subset
         * @param string $keep
         * @return \Polars\LazyFrame
         */
        public function unique(?array $subset = null, string $keep = "first"): \Polars\LazyFrame {}

        /**
         * Aggregate the columns to their variance
         * @return \Polars\LazyFrame
         *
         * @param int $ddof
         * @return \Polars\LazyFrame
         */
        public function variance(int $ddof = 0): \Polars\LazyFrame {}

        /**
         * Get number of columns
         * @return int
         *
         * @return int
         */
        public function width(): int {}

        /**
         * Add or modify columns
         * @param \Polars\Expr[] $expressions
         * @return \Polars\LazyFrame
         *
         * @param array $expressions
         * @return \Polars\LazyFrame
         */
        public function withColumns(array $expressions): \Polars\LazyFrame {}

        /**
         * Add a row index column
         * @return \Polars\LazyFrame
         *
         * @param string $name
         * @param int $offset
         * @return \Polars\LazyFrame
         */
        public function withRowIndex(string $name = "index", int $offset = 0): \Polars\LazyFrame {}
    }

    class LazyGroupBy {
        public function __construct() {}

        /**
         * Aggregate using expressions
         * @param \Polars\Expr[] $expressions
         * @return \Polars\LazyFrame
         *
         * @param array $expressions
         * @return \Polars\LazyFrame
         */
        public function agg(array $expressions): \Polars\LazyFrame {}

        /**
         * Count rows per group
         * @return \Polars\LazyFrame
         *
         * @return \Polars\LazyFrame
         */
        public function count(): \Polars\LazyFrame {}

        /**
         * First row per group
         * @return \Polars\LazyFrame
         *
         * @return \Polars\LazyFrame
         */
        public function first(): \Polars\LazyFrame {}

        /**
         * First n rows per group
         * @return \Polars\LazyFrame
         *
         * @param int $n
         * @return \Polars\LazyFrame
         */
        public function head(int $n = 5): \Polars\LazyFrame {}

        /**
         * Last row per group
         * @return \Polars\LazyFrame
         *
         * @return \Polars\LazyFrame
         */
        public function last(): \Polars\LazyFrame {}

        /**
         * Max per group
         * @return \Polars\LazyFrame
         *
         * @return \Polars\LazyFrame
         */
        public function max(): \Polars\LazyFrame {}

        /**
         * Mean per group
         * @return \Polars\LazyFrame
         *
         * @return \Polars\LazyFrame
         */
        public function mean(): \Polars\LazyFrame {}

        /**
         * Median per group
         * @return \Polars\LazyFrame
         *
         * @return \Polars\LazyFrame
         */
        public function median(): \Polars\LazyFrame {}

        /**
         * Min per group
         * @return \Polars\LazyFrame
         *
         * @return \Polars\LazyFrame
         */
        public function min(): \Polars\LazyFrame {}

        /**
         * Sum per group
         * @return \Polars\LazyFrame
         *
         * @return \Polars\LazyFrame
         */
        public function sum(): \Polars\LazyFrame {}

        /**
         * Last n rows per group
         * @return \Polars\LazyFrame
         *
         * @param int $n
         * @return \Polars\LazyFrame
         */
        public function tail(int $n = 5): \Polars\LazyFrame {}
    }

    enum QuantileMethod {
      case Nearest;
      case Lower;
      case Higher;
      case Midpoint;
      case Linear;
      case Equiprobable;
    }

    class Series implements \ArrayAccess, \Countable {
        /**
         * Get the data type of the Series
         *
         * @var \Polars\DataType
         */
        public readonly \Polars\DataType $dtype;

        /**
         * Get the name of the Series
         *
         * @var string
         */
        public readonly string $name;

        /**
         * Get the shape of the Series as [length]
         *
         * @var array
         */
        public readonly array $shape;

        /**
         * Create a new Series from a PHP array
         *
         * @param string $name
         * @param array $values
         */
        public function __construct(string $name = "", array $values) {}

        /**
         * Display the Series
         *
         * @return string
         */
        public function __toString(): string {}

        /**
         * Create an alias for the Series (same as rename)
         *
         * @param string $name
         * @return \Polars\Series
         */
        public function alias(string $name): \Polars\Series {}

        /**
         * Check if all values are true (for boolean Series)
         *
         * @return bool
         */
        public function all(): bool {}

        /**
         * Check if any value is true (for boolean Series)
         *
         * @return bool
         */
        public function any(): bool {}

        /**
         * Get the index of the maximum value
         *
         * @return mixed
         */
        public function argMax(): mixed {}

        /**
         * Get the index of the minimum value
         *
         * @return mixed
         */
        public function argMin(): mixed {}

        /**
         * Cast Series to a different data type
         * @param string $dtype One of: 'int8', 'int16', 'int32', 'int64', 'uint8', 'uint16', 'uint32', 'uint64', 'float32', 'float64', 'bool', 'string'
         *
         * @param string $dtype
         * @return \Polars\Series
         */
        public function cast(string $dtype): \Polars\Series {}

        /**
         * Create a copy of the Series
         *
         * @return \Polars\Series
         */
        public function copy(): \Polars\Series {}

        /**
         * Get the number of elements (Countable interface)
         *
         * @return int
         */
        public function count(): int {}

        /**
         * Count non-null values
         *
         * @return int
         */
        public function countNonNull(): int {}

        /**
         * Remove null values
         *
         * @return \Polars\Series
         */
        public function dropNulls(): \Polars\Series {}

        /**
         * Element-wise equality comparison
         * @param int|float|string|bool|null $other
         *
         * @param mixed $other
         * @return \Polars\Series
         */
        public function eq(mixed $other): \Polars\Series {}

        /**
         * Fill null values using backward strategy
         *
         * @return \Polars\Series
         */
        public function fillNullBackward(): \Polars\Series {}

        /**
         * Fill null values using forward strategy
         *
         * @return \Polars\Series
         */
        public function fillNullForward(): \Polars\Series {}

        /**
         * Fill null values with the mean
         *
         * @return \Polars\Series
         */
        public function fillNullMean(): \Polars\Series {}

        /**
         * Fill null values with zero
         *
         * @return \Polars\Series
         */
        public function fillNullZero(): \Polars\Series {}

        /**
         * Get the first element
         *
         * @return mixed
         */
        public function first(): mixed {}

        /**
         * Element-wise greater than or equal comparison
         * @param int|float|string|bool|null $other
         *
         * @param mixed $other
         * @return \Polars\Series
         */
        public function ge(mixed $other): \Polars\Series {}

        /**
         * Get flags that are set on the Series
         *
         * @return array
         */
        public function getFlags(): array {}

        /**
         * Element-wise greater than comparison
         * @param int|float|string|bool|null $other
         *
         * @param mixed $other
         * @return \Polars\Series
         */
        public function gt(mixed $other): \Polars\Series {}

        /**
         * Get the first n elements
         *
         * @param int $n
         * @return \Polars\Series
         */
        public function head(int $n = 10): \Polars\Series {}

        /**
         * Aggregate all values into a single list
         *
         * @return \Polars\Series
         */
        public function implode(): \Polars\Series {}

        /**
         * Check if Series is empty
         *
         * @return bool
         */
        public function isEmpty(): bool {}

        /**
         * Check which values are NaN
         *
         * @return \Polars\Series
         */
        public function isNan(): \Polars\Series {}

        /**
         * Check which values are not NaN
         *
         * @return \Polars\Series
         */
        public function isNotNan(): \Polars\Series {}

        /**
         * Check which values are not null
         *
         * @return \Polars\Series
         */
        public function isNotNull(): \Polars\Series {}

        /**
         * Check which values are null
         *
         * @return \Polars\Series
         */
        public function isNull(): \Polars\Series {}

        /**
         * Get a single value from the Series (must have exactly one element)
         *
         * @return mixed
         */
        public function item(): mixed {}

        /**
         * Get the last element
         *
         * @return mixed
         */
        public function last(): mixed {}

        /**
         * Element-wise less than or equal comparison
         * @param int|float|string|bool|null $other
         *
         * @param mixed $other
         * @return \Polars\Series
         */
        public function le(mixed $other): \Polars\Series {}

        /**
         * Get the number of elements in the Series
         *
         * @return int
         */
        public function len(): int {}

        /**
         * Element-wise less than comparison
         * @param int|float|string|bool|null $other
         *
         * @param mixed $other
         * @return \Polars\Series
         */
        public function lt(mixed $other): \Polars\Series {}

        /**
         * Get the maximum value
         *
         * @return mixed
         */
        public function max(): mixed {}

        /**
         * Get value from this Series at the index of the maximum of another Series
         *
         * @param \Polars\Series $other
         * @return mixed
         */
        public function maxBy(\Polars\Series $other): mixed {}

        /**
         * Get the mean of all values
         *
         * @return float
         */
        public function mean(): float {}

        /**
         * Get the median of all values
         *
         * @return float
         */
        public function median(): float {}

        /**
         * Get the minimum value
         *
         * @return mixed
         */
        public function min(): mixed {}

        /**
         * Get value from this Series at the index of the minimum of another Series
         *
         * @param \Polars\Series $other
         * @return mixed
         */
        public function minBy(\Polars\Series $other): mixed {}

        /**
         * Get the mode (most common value(s))
         *
         * @return \Polars\Series
         */
        public function mode(): \Polars\Series {}

        /**
         * Count unique values
         *
         * @return int
         */
        public function nUnique(): int {}

        /**
         * Get the maximum value, propagating NaN
         *
         * @return mixed
         */
        public function nanMax(): mixed {}

        /**
         * Get the minimum value, propagating NaN
         *
         * @return mixed
         */
        public function nanMin(): mixed {}

        /**
         * Element-wise inequality comparison
         * @param int|float|string|bool|null $other
         *
         * @param mixed $other
         * @return \Polars\Series
         */
        public function ne(mixed $other): \Polars\Series {}

        /**
         * Count null values
         *
         * @return int
         */
        public function nullCount(): int {}

        /**
         * Check if an index exists
         *
         * @param mixed $offset
         * @return bool
         */
        public function offsetExists(mixed $offset): bool {}

        /**
         * Get value at index
         *
         * @param mixed $offset
         * @return mixed
         */
        public function offsetGet(mixed $offset): mixed {}

        /**
         * Set value at index - not supported
         * @return void
         *
         * @param mixed $_offset
         * @param mixed $_value
         * @return void
         */
        public function offsetSet(mixed $_offset, mixed $_value): void {}

        /**
         * Unset value at index - not supported
         * @return void
         *
         * @param mixed $_offset
         * @return void
         */
        public function offsetUnset(mixed $_offset): void {}

        /**
         * Get the product of all values
         *
         * @return mixed
         */
        public function product(): mixed {}

        /**
         * Get the quantile value
         * @param string $method One of: nearest, lower, higher, midpoint, linear, equiprobable
         *
         * @param float $quantile
         * @param string $method
         * @return mixed
         */
        public function quantile(float $quantile, string $method = "linear"): mixed {}

        /**
         * Rename the Series
         *
         * @param string $name
         * @return \Polars\Series
         */
        public function rename(string $name): \Polars\Series {}

        /**
         * Reverse the Series
         *
         * @return \Polars\Series
         */
        public function reverse(): \Polars\Series {}

        /**
         * Extract a slice of the Series
         *
         * @param int $offset
         * @param int $length
         * @return \Polars\Series
         */
        public function slice(int $offset, int $length): \Polars\Series {}

        /**
         * Sort the Series
         *
         * @param bool $descending
         * @param bool $nullsLast
         * @return \Polars\Series
         */
        public function sort(bool $descending = false, bool $nullsLast = true): \Polars\Series {}

        /**
         * Get the standard deviation
         *
         * @param int $ddof
         * @return float
         */
        public function std(int $ddof = 1): float {}

        /**
         * Get the sum of all values
         *
         * @return mixed
         */
        public function sum(): mixed {}

        /**
         * Get the last n elements
         *
         * @param int $n
         * @return \Polars\Series
         */
        public function tail(int $n = 10): \Polars\Series {}

        /**
         * Convert Series to PHP array
         *
         * @return array
         */
        public function toArray(): array {}

        /**
         * Get unique values
         *
         * @return \Polars\Series
         */
        public function unique(): \Polars\Series {}

        /**
         * Get the variance
         *
         * @param int $ddof
         * @return float
         */
        public function variance(int $ddof = 1): float {}
    }
}
