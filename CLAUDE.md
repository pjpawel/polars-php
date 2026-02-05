# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Polars-PHP is a PHP extension that provides Pola.rs DataFrame functionality for PHP. It's built using `ext-php-rs` to create Rust-based PHP extensions.

## Build and Test Commands

All commands must be run from the `php/` directory:

```bash
# Build and generate PHP stubs (preferred over raw cargo build)
composer stubs:debug

# Run tests
composer test:debug

# Build only (no stubs)
composer build:debug

# Full workflow: build, generate stubs, test
composer all:debug

# Release builds
composer build:release
composer test:release
composer all:release
```

To run a single test:
```bash
php -d extension=../target/debug/libpolars_php.so vendor/bin/phpunit tests/SimpleDataFrameTest.php
php -d extension=../target/debug/libpolars_php.so vendor/bin/phpunit --filter testSelect tests/SimpleDataFrameTest.php
```

## Architecture

### Rust Source (`src/`)

- **lib.rs**: Module entry point, registers PHP classes/enums via `#[php_module]`
- **data_frame.rs**: `PhpDataFrame` class (`Polars\DataFrame`) - wraps Polars DataFrame, handles CSV I/O, array access, aggregations
- **lazy_frame.rs**: `PhpLazyFrame` class (`Polars\LazyFrame`) - wraps Polars LazyFrame for lazy evaluation with query optimization
- **lazy_group_by.rs**: `PhpLazyGroupBy` class (`Polars\LazyGroupBy`) - wraps grouped lazy operations, stores LazyFrame + group-by expressions
- **series.rs**: `PhpSeries` class (`Polars\Series`) - wraps Polars Series, one-dimensional array with element access, aggregations, comparisons
- **expression.rs**: `PolarsExpr` class (`Polars\Expr`) - expression builder for column operations, comparisons, aggregations
- **data_type.rs**: `PolarsDataType` class (`Polars\DataType`) - wrapper for Polars data types
- **exception.rs**: `PolarsException` (`Polars\Exception`) - custom exception type, defines `ExtResult<T>` alias
- **common.rs**: Shared utilities (`any_value_to_zval`, `extract_exprs` for converting Polars values to PHP)

### Key Patterns

**PHP Class Definition:**
```rust
#[php_class]
#[php(name = "Polars\\ClassName")]
pub struct PhpClassName { inner: PolarsType }

#[php_impl]
#[php(change_method_case = "camelCase")]
impl PhpClassName { ... }
```

**Error Handling:** Use `ExtResult<T>` (alias for `Result<T, PolarsException>`) and return `PolarsException::new(msg)` for errors.

**Zval Conversion:** Convert PHP values to `Polars\Expr` types via `zval_to_expr()` in expression.rs.

**Property Getters:** Use `#[php(getter)]` with `get_` prefix for PHP property-style access:
```rust
#[php(getter)]
pub fn get_name(&self) -> String { ... }  // Accessed as $obj->getName() in PHP
```

**Boolean Type Handling:** PHP 8+ has separate `True` and `False` types. When matching PHP types, include all three:
```rust
PhpDataType::Bool | PhpDataType::True | PhpDataType::False => { ... }
```

**Selector vs Expr:** In Polars 0.51, `polars_plan::dsl::all()` returns `Selector`, not `Expr`. Use `.as_expr()` or `Expr::from(all())` to convert. Methods like `drop()`, `unique()`, `drop_nulls()` on `LazyFrame` take `Selector` type (use `Selector::ByName { names, strict }` for column name lists).

**LazyGroupBy storage:** Polars `LazyGroupBy` consumes self on method calls. Store `LazyFrame` + `Vec<Expr>` and reconstruct the `LazyGroupBy` for each operation (same pattern as python-polars bindings).

### PHP Stubs

Generated stub file `php/polars.stubs.php` provides IDE autocompletion. Regenerated via `cargo php stubs -o php/polars.stubs.php`.

### PHP Tests (`php/tests/`)

PHPUnit tests validate the extension. Extension is loaded dynamically via `-d extension=` flag.

## Dependencies

- **ext-php-rs**: Rust-PHP bindings framework
- **polars**: DataFrame library (with lazy, csv, parquet, json features)
- **polars-plan**: Expression DSL (with mode, is_between features)

## Documentation (`doc/`)

Documentation is built using Sphinx with MyST Markdown. Source files:

- **index.md**: Main documentation index
- **dataframe.md**: DataFrame class API reference
- **lazyframe.md**: LazyFrame class API reference
- **lazygroupby.md**: LazyGroupBy class API reference
- **series.md**: Series class API reference
- **expr.md**: Expr class API reference
- **datatype.md**: DataType class API reference
- **exception.md**: Exception class API reference
- **closedinterval.md**: ClosedInterval enum API reference

Build documentation:
```bash
cd doc
make install  # Install dependencies (first time only)
make html     # Build HTML documentation
```

Output is generated in `doc/_build/html/`.
