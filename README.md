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
- Linux, macOS or Windows
- Rust toolchain (for building from source; on Windows use the nightly toolchain, see below)

## Documentation

Documentation is available at [https://pjpawel.github.io/polars-php/](https://pjpawel.github.io/polars-php/).

## Quick Start

```php
<?php

// Load the extension
// php -d extension=/path/to/libpolars_php.so script.php   (Linux)
// php -d extension=/path/to/libpolars_php.dylib script.php (macOS)
// php -d extension=C:\path\to\polars_php.dll script.php    (Windows)
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

### Install with PIE (Packagist)
The extension is published as a [PIE](https://github.com/php/pie) package on Packagist:

```bash
pie install pjpawel/polars-php
```

PIE downloads the source distribution and runs the standard `phpize` / `./configure` / `make` / `make install` flow. The actual compilation is delegated to `cargo` (see `pie/config.m4` and `pie/Makefile.frag`), so the [Rust toolchain](https://rustup.rs) and the PHP development files (`phpize`) must be installed. When finished, the extension is enabled as `extension=polars` in your `php.ini`.

Verify with:

```bash
php -m | grep polars
```

### Pre-built Binaries
Pre-built binaries are available for Linux, macOS and Windows.
There were compiled for PHP 8.3, 8.4, 8.5 and 8.6.
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

#### Windows

On Windows the extension builds as `polars_php.dll` and requires:

- the **nightly** Rust toolchain (ext-php-rs uses the unstable `abi_vectorcall` ABI on Windows, see `src/lib.rs`)
- LLVM installed, so `libclang` is available for the bindgen step:

```powershell
choco install llvm
$env:LIBCLANG_PATH = "C:\Program Files\LLVM\bin"
rustup toolchain install nightly
```

The PHP development files (headers and `php8.lib`/`php8ts.lib`) are downloaded automatically by the build script, based on the `php` binary found in `PATH`.

```powershell
# Build (from the repository root)
cargo +nightly build --release

# Verify installation
php -d extension=target\release\polars_php.dll -m

# Optional: run the PHPUnit tests (from the php/ directory)
cd php
composer install
php -d extension=..\target\release\polars_php.dll vendor/bin/phpunit tests
```

The `composer` scripts in `php/composer.json` reference the `.so` file and are intended for Linux/macOS; on Windows pass the DLL path to `php -d extension=` directly, as shown above.

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
