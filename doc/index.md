# Polars-PHP Documentation

A PHP extension bringing the power of [Polars](https://pola.rs/) DataFrames to PHP.

```{note}
Some features are not yet available. Look in GitHub issues for updates.
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

# Build the extension
composer build:relese

# Verify installation
php -d extension=../target/release/libpolars_php.so -m | grep polars
```

## Quick Example

```php
<?php

use Polars\DataFrame;
use Polars\Expr;

// Create a DataFrame from array
$df = new DataFrame([
    'name' => ['Alice', 'Bob', 'Charlie'],
    'age' => [25, 30, 35],
    'city' => ['NYC', 'LA', 'Chicago']
]);

echo $df;

// Filter using expressions
$expr = Expr::col('age')->gt(25);
$result = $df->select([$expr]);
```

## API Reference

```{toctree}
:maxdepth: 2
:caption: Classes

dataframe
lazyframe
lazygroupby
series
expr
datatype
exception
closedinterval
quantilemethod
```

## Requirements

- PHP 8.3+
- Linux or macOS (Windows not currently supported)
- Rust toolchain (for building from source)

## License

MIT License – see [LICENSE](https://github.com/pjpawel/polars-php/blob/main/LICENSE) for details.
