# Polars-PHP Documentation

A PHP extension bringing the power of [Polars](https://pola.rs/) DataFrames to PHP.

```{note}
This extension is under active development. Some features are not yet available.
```

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
series
expr
datatype
exception
closedinterval
```

## Requirements

- PHP 8.3+
- Linux or macOS (Windows not currently supported)
- Rust toolchain (for building from source)

## License

MIT License - see [LICENSE](https://github.com/pjpawel/polars-php/blob/main/LICENSE) for details.
