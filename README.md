# Polars-PHP

A PHP extension bringing the power of [Polars](https://pola.rs/) DataFrames to PHP.

> **Note:** Some features are not yet available. Look in GitHub issues for updates.

## Features

- Fast, memory-efficient DataFrames powered by Rust
- Read/write CSV files with automatic type inference
- Expressive column operations via `Expr` API
- Array-like access to rows and columns
- Aggregation functions (min, max, mean, std, count, etc.)

## Requirements

- PHP 8.3+
- Linux or macOS (Windows not currently supported)
- Rust toolchain (for building from source)

## Documentation

Documentation is available at [https://pjpawel.github.io/polars-php/](https://pjpawel.github.io/polars-php/).

## Quick Start

```php
<?php

// Load the extension
// php -d extension=/path/to/libpolars_php.so script.php
// or include extension in php.ini

use Polars\DataFrame;
use Polars\Expr;

// Create a DataFrame from array
$df = new DataFrame([
    'name' => ['Alice', 'Bob', 'Charlie'],
    'age' => [25, 30, 35],
    'city' => ['NYC', 'LA', 'Chicago']
]);

echo $df;
// shape: (3, 3)
// ┌─────────┬─────┬─────────┐
// │ name    ┆ age ┆ city    │
// │ ---     ┆ --- ┆ ---     │
// │ str     ┆ i64 ┆ str     │
// ╞═════════╪═════╪═════════╡
// │ Alice   ┆ 25  ┆ NYC     │
// │ Bob     ┆ 30  ┆ LA      │
// │ Charlie ┆ 35  ┆ Chicago │
// └─────────┴─────┴─────────┘

// Access columns and rows
$ages = $df['age'];           // Single column
$firstRow = $df[0];           // First row
$subset = $df[['name', 'age']]; // Multiple columns
$specificRow = $df[['col1', 1]]; //Specific row

// Read from CSV
$df = DataFrame::readCsv('data.csv');

// Expressions for filtering and transformations
$expr = Expr::col('age')->gt(25);
$result = $df->select([$expr]);

// Aggregations
$df->max();
$df->mean();
$df->min();
```

## Installation

### Pre-built Binaries
Pre-built binaries are available for Linux and macOS.
There were compiled for PHP 8.3, 8.4 and 8.5.
Download the appropriate binary for your platform from the [releases](https://github.com/pjpawel/polars-php/releases) page.

### Building from Source

```bash
# Clone the repository
git clone https://github.com/pjpawel/polars-php.git
cd polars-php/php

# Build extension for release
composer build:release

# Or build the extension for debug
# composer build:debug

# Verify installation
php -d extension=../target/release/libpolars_php.so -m | grep polars

# Optional: Run php tests
composer test:debug
```

## Benchmarks

The project includes a [PHPBench](https://phpbench.readthedocs.io/) benchmark suite comparing polars-php against pure PHP equivalents.

### Running Benchmarks

```bash
cd php
composer install
composer bench           # Run all benchmarks
composer bench:polars    # Run only Polars benchmarks
composer bench:purephp   # Run only pure PHP benchmarks
```

### Benchmark Operations

| Operation        | Polars-PHP                       | Pure PHP Equivalent        |
|------------------|----------------------------------|----------------------------|
| DataFrame Create | `new DataFrame($data)`           | Array assignment           |
| CSV Read         | `DataFrame::readCsv($path)`      | `fgetcsv()` loop           |
| CSV Write        | `$df->writeCsv($path)`           | `fputcsv()` loop           |
| Aggregation      | `$df->sum()`, `mean()`, etc.     | `array_sum()`, manual calc |
| Filter           | `$df->filter(Expr::col()->gt())` | `array_filter()`           |
| Sort             | `$df->sort('column')`            | `usort()`                  |
| Join             | `$df->join($df2, ...)`           | Hash-join with `foreach`   |
| Column Access    | `$df['column']`                  | `array_column()`           |

## Contributing

Contributions are welcome! Please feel free to submit issues and pull requests.

## License

Apache 2.0 License – see [LICENSE](./LICENSE) for details.
