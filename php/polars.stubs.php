<?php

// Stubs for polars-php

namespace Polars {
    class Exception extends \Exception {
        public function __construct() {}
    }

    class DataFrame implements \ArrayAccess {
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
        public function offsetSet(mixed $_offset, mixed $_value): mixed {}

        /**
         * Unset value at offset - not supported for DataFrames
         * @return void
         */
        public function offsetUnset(mixed $_offset): mixed {}

        /**
         * Get columns names
         * @returns string[]
         */
        public function getColumns(): array {}

        /**
         * Set columns names
         * @param string[] $columns - length of list must be equal to current length of columns
         * @return void
         */
        public function setColumns(array $columns): mixed {}

        /**
         * @return \Polars\DataType[]
         */
        public function dtypes(): array {}

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

        public static function fromCsv(string $path, bool $headerIncluded = true, string $separator = ","): \Polars\DataFrame {}

        /**
         * Write to CSV file
         */
        public function writeCsv(string $path, bool $includeHeader, string $separator = ","): mixed {}

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

        public function isBetween(mixed $lowerBound, mixed $upperBound, \Polars\ClosedInterval $closed): \Polars\Expr {}

        /**
         * Constructor creates LiteralValue from int, float, string, boolean, or null. Passing other values will cause throwing exception
         * @throws Polars\Exception
         */
        public function __construct(mixed $value) {}
    }

    class DataType {
        public function __construct() {}
    }

    enum ClosedInterval {
      case Both;
      case Left;
      case Right;
      case None;
    }
}
