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
         * @param
         * - length of list must be equal to current length of columns
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

        /**
         * Write to CSV file
         */
        public function writeCsv(string $path, bool $includeHeader): mixed {}

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
        public function __construct(array $data, ?bool $by_keys) {}
    }

    class Expr {
        public static function col(string $name): \Polars\Expr {}

        public static function cols(array $names): \Polars\Expr {}

        public static function all(): \Polars\Expr {}

        public function eq(\Polars\Expr $other): \Polars\Expr {}

        /**
         * Constructor calls col static method
         */
        public function __construct(string $name) {}
    }

    class DataType {
        public function __construct() {}
    }
}
