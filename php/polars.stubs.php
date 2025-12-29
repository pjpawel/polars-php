<?php

// Stubs for polars-php

namespace Polars {
    class DataFrame {
        /**
         * Get columns names
         */
        public function getColumns(): array {}

        /**
         * Set columns names
         * @param string[] $columns - length of list must be equal to current length of columns
         */
        public function setColumns(array $columns): mixed {}

        /**
         * Return list of
         */
        public function dtypes(): array {}

        /**
         * Get the number of rows
         */
        public function height(): int {}

        /**
         * Get the shape of the DataFrame as [rows, columns]
         */
        public function shape(): array {}

        /**
         * Get the number of columns
         */
        public function width(): int {}

        /**
         * Return the number of non-null elements for each column.
         */
        public function count(): \Polars\DataFrame {}

        /**
         * Aggregate the columns of this DataFrame to their maximum value.
         */
        public function max(): \Polars\DataFrame {}

        /**
         * Get the maximum value horizontally across columns.
         */
        public function maxHorizontal(): \Polars\DataFrame {}

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
        public function std(?int $ddof): \Polars\DataFrame {}

        /**
         * Make select based on given expressions
         * @param \Polars\Expr[]
         */
        public function select(array $expressions): \Polars\DataFrame {}

        /**
         * Get the first n rows
         */
        public function head(?int $n): \Polars\DataFrame {}

        /**
         * Get the last n rows
         */
        public function tail(?int $n): \Polars\DataFrame {}

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

        public static function fromCsv(string $path, ?bool $headerIncluded, ?string $separator): \Polars\DataFrame {}

        /**
         * Write to CSV file
         */
        public function writeCsv(string $path, bool $includeHeader, ?string $separator): mixed {}

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
        public function __construct(array $data, bool $byKeys) {}
    }

    class Expr {
        public static function col(string $name): \Polars\Expr {}

        public static function cols(array $names): \Polars\Expr {}

        public static function all(): \Polars\Expr {}

        public function any(?bool $ignoreNulls): \Polars\Expr {}

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

        public function std(?int $ddof): \Polars\Expr {}

        public function sum(): \Polars\Expr {}

        public function var(?int $ddof): \Polars\Expr {}

        /**
         * Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function eq(mixed $other): \Polars\Expr {}

        /**
         * Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function eqMissing(mixed $other): \Polars\Expr {}

        /**
         * Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function ge(mixed $other): \Polars\Expr {}

        /**
         * Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function gt(mixed $other): \Polars\Expr {}

        /**
         * Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function le(mixed $other): \Polars\Expr {}

        /**
         * Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function lt(mixed $other): \Polars\Expr {}

        /**
         * Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function ne(mixed $other): \Polars\Expr {}

        /**
         * Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function neqMissing(mixed $other): \Polars\Expr {}

        /**
         * Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function add(mixed $other): \Polars\Expr {}

        /**
         * Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function floorDiv(mixed $other): \Polars\Expr {}

        /**
         * Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function modulo(mixed $other): \Polars\Expr {}

        /**
         * Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function mul(mixed $other): \Polars\Expr {}

        public function neg(): \Polars\Expr {}

        /**
         * Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function pow(mixed $other): \Polars\Expr {}

        /**
         * Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function sub(mixed $other): \Polars\Expr {}

        /**
         * Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function div(mixed $other): \Polars\Expr {}

        /**
         * Accepts numeric, string, bool, null or PolarsExpr object
         */
        public function xor(mixed $other): \Polars\Expr {}

        public function hasNulls(): \Polars\Expr {}

        /**
         * Constructor creates LiteralValue from int, float, string, boolean, or null. Passing other values will cause throwing exception
         * @throws Polars\Exception
         */
        public function __construct(mixed $value) {}
    }

    class DataType {
        public function __construct() {}
    }
}
