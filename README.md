# Polars-PHP

A PHP extension bringing the power of [Polars](https://pola.rs/) DataFrames to PHP.

> **Note:** Some features are not yet available.

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

### Building from Source

```bash
# Clone the repository
git clone https://github.com/pjpawel/polars-php.git
cd polars-php/php

# Build extension for debug
composer build:debug

# Or build the extension for release
# composer build:release

# Verify installation
php -d extension=../target/debug/libpolars_php.so -m | grep polars

# Run php tests
composer test:debug
```

## Contributing

Contributions are welcome! Please feel free to submit issues and pull requests.

## License

Apache 2.0 License – see [LICENSE](./LICENSE) for details.
