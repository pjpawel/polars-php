# Polars-PHP

A PHP extension bringing the power of [Polars](https://pola.rs/) DataFrames to PHP.

> **Note:** This extension is under active development. Some features are not yet available. See the [compatibility list](./compatibilty) for current status.

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

## Installation

### Building from Source

```bash
# Clone the repository
git clone https://github.com/pjpawel/polars-php.git
cd polars-php

# Install PHP dependencies
cd php && composer install

# Build the extension
composer build:debug

# Verify installation
php -d extension=../target/debug/libpolars_php.so -m | grep polars
```

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
$df = DataFrame::fromCsv('data.csv');

// Expressions for filtering and transformations
$expr = Expr::col('age')->gt(25);
$result = $df->select([$expr]);

// Aggregations
$df->max();
$df->mean();
$df->min();
```

## API Overview

### DataFrame

| Method                                 | Description                   |
|----------------------------------------|-------------------------------|
| `new DataFrame(array $data)`           | Create from associative array |
| `DataFrame::fromCsv(string $path)`     | Read from CSV file            |
| `writeCsv(string $path)`               | Write to CSV file             |
| `head(int $n = 10)`                    | Get first n rows              |
| `tail(int $n = 10)`                    | Get last n rows               |
| `select(array $exprs)`                 | Select/transform columns      |
| `height()` / `width()` / `shape()`     | Dimensions                    |
| `min()` / `max()` / `mean()` / `std()` | Aggregations                  |

### Expr

| Method                                         | Description                |
|------------------------------------------------|----------------------------|
| `Expr::col(string $name)`                      | Reference a column         |
| `Expr::cols(array $names)`                     | Reference multiple columns |
| `eq()`, `ne()`, `gt()`, `lt()`, `ge()`, `le()` | Comparisons                |
| `add()`, `sub()`, `mul()`, `div()`, `pow()`    | Arithmetic                 |
| `sum()`, `mean()`, `min()`, `max()`, `count()` | Aggregations               |
| `isBetween($lower, $upper, $closed)`           | Range check                |

## Documentation

See the [compatibility directory](./compatibilty) for detailed method availability compared to Python Polars.

## Contributing

Contributions are welcome! Please feel free to submit issues and pull requests.

## License

MIT License - see [LICENSE](./LICENSE) for details.
