# AGENTS.md

This file provides guidance to coding agent when working with code in this repository.

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

## Platform Support

- **Linux/macOS**: build with the stable toolchain; the extension is produced as `target/<profile>/libpolars_php.so` (Linux) or `libpolars_php.dylib` (macOS). The composer scripts above hardcode the `.so` path and are meant for Linux/macOS.
- **Windows**: build with the **nightly** toolchain (`cargo +nightly build`) — `src/lib.rs` enables the unstable `abi_vectorcall` feature on Windows. The extension is produced as `target/<profile>/polars_php.dll`. `.cargo/config.toml` already selects the `rust-lld` linker for `*-pc-windows-msvc` targets and disables `dynamic_lookup` there (not needed/possible on Windows; the build links against `php8.lib`/`php8ts.lib` from the PHP devel pack, which ext-php-rs downloads automatically based on the `php` binary in `PATH`). LLVM must be installed for libclang (bindgen). On Windows, pass the DLL directly, e.g. from `php/`: `php -d extension=../target/debug/polars_php.dll vendor/bin/phpunit tests`.
- CI (`.github/workflows/build.yaml`) builds and tests ubuntu/macos/windows for PHP 8.3-8.6; Windows uses the nightly toolchain and `+crt-static`.

## Architecture

### Rust Source (`src/`)

- **lib.rs**: Module entry point, registers PHP classes/enums via `#[php_module]`
- **data_frame.rs**: `PhpDataFrame` class (`Polars\DataFrame`) - wraps Polars DataFrame, handles IO (CSV, JSON, NDJSON, Parquet), array access, aggregations
- **lazy_frame.rs**: `PhpLazyFrame` class (`Polars\LazyFrame`) - wraps Polars LazyFrame for lazy evaluation with query optimization, scan (CSV, NDJSON, Parquet) and sink methods
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

**Selector vs Expr:** In Polars 0.55, `polars_plan::dsl::all()` returns `Selector`, not `Expr`. Use `.as_expr()` or `Expr::from(all())` to convert. Methods like `drop()`, `unique()`, `drop_nulls()` on `LazyFrame` take `Selector` type (use `Selector::ByName { names, strict }` for column name lists). `UnpivotArgsDSL.on` is `Option<Selector>` (index stays `Selector`). `LazyFrame::explode` takes `(Selector, ExplodeOptions)` — construct `ExplodeOptions { empty_as_null: true, keep_nulls: true }` explicitly (no `Default`).

**LazyGroupBy storage:** Polars `LazyGroupBy` consumes self on method calls. Store `LazyFrame` + `Vec<Expr>` and reconstruct the `LazyGroupBy` for each operation (same pattern as python-polars bindings).

**PlRefPath Construction:** In Polars 0.55, `PlPath` is no longer an enum; use `PlRefPath` (exported from `polars::prelude`), which implements `From<&str>`:

```rust
PlRefPath::from(path.as_str())
```

Required by `LazyCsvReader::new()`, `LazyJsonLineReader::new()`, `LazyFrame::scan_parquet()`, and `SinkTarget::Path()`.

**LazyCsvReader parse options:** Use `map_parse_options(|opts| opts.with_separator(sep))`, not `with_parse_options()`.

**DataFrame construction:** `DataFrame::new()` now takes `(height, columns)` — use `DataFrame::new_infer_height(columns)` instead. `get_columns()` was renamed to `columns()` (returns `&[Column]`); `DataFrame::is_empty()` was removed — use `height() == 0`.

**LazyFrame sinks:** `sink_csv`/`sink_parquet`/`sink_json` were removed. Use the unified API:

```rust
lf.sink(
    SinkDestination::File { target: SinkTarget::Path(PlRefPath::from(path.as_str())) },
    FileWriteFormat::Csv(csv_writer_options),        // or Parquet(Arc<ParquetWriteOptions>), NDJson(NDJsonWriterOptions)
    UnifiedSinkArgs::default(),
)?.collect()?
```

`CsvWriterOptions.serialize_options` is `Arc<SerializeOptions>` — wrap with `Arc::new(...)`. `JsonWriterOptions` is now `NDJsonWriterOptions`.

**Pivot:** The eager `pivot_stable` is gone; pivot is lazy-only: `LazyFrame::pivot(on: Selector, on_columns: Arc<DataFrame>, index: Selector, values: Selector, agg: Expr, maintain_order: bool, separator: PlSmallStr, column_naming: PivotColumnNaming)`. Compute `on_columns` yourself (distinct `on` values, optionally sorted), and build `agg` from `element()` (e.g. `element().sum()`) or `len()` — explicit `col(...)` references are rejected.

**Misc 0.55 changes:** `LazyFrame::merge_sorted` takes `(other, key: impl IntoIterator<Item = impl Into<PlSmallStr>>, maintain_order: bool)`; `sample_n`/`sample_frac` take `shuffle: Option<bool>`; `Expr::implode`/`Expr::mode` and `polars_ops::chunked_array::mode::mode` take a `maintain_order: bool`.

### PHP Stubs

Generated stub file `php/polars.stubs.php` provides IDE autocompletion. Regenerated via `cargo php stubs -o php/polars.stubs.php`.

### PHP Tests (`php/tests/`)

PHPUnit tests validate the extension. Extension is loaded dynamically via `-d extension=` flag.
Do not add rust tests.

## Dependencies

- **ext-php-rs**: Rust-PHP bindings framework
- **polars**: DataFrame library (with lazy, csv, parquet, json, round_series, product, mode, is_between, timezones features)
- **polars-plan**: Expression DSL (mode, is_between features provided via polars umbrella crate)

## Change Requirements

Every change to the codebase MUST include all three of the following:

1. **Documentation** — update the relevant `doc/*.md` file(s) with the new/changed API
2. **Tests** — add or update PHPUnit tests in `php/tests/` covering the change
3. **Changelog** — add an entry to `CHANGELOG.md` describing the change
4. **Benchmark** - if necessary add benchmark in php/benchmarks
5. **AGENTS.md** - update AGENTS.md file if there is any structural change to the project

Do not consider a task complete until first three are updated.

Every task must end building extension and run all tests

## Benchmarks (`php/benchmarks/`)

PHPBench suite comparing polars-php vs pure PHP. All benchmark files live under `php/benchmarks/`.

### Structure

- **`phpbench.json`**: Runner config — loads the release extension, sets 4G memory limit, enables OPcache CLI
- **`composer.json`**: _(none — uses `php/composer.json` directly)_
- **`Fixtures/DataGenerator.php`**: Shared data generation at sizes `[100, 1_000, 10_000, 100_000, 1_000_000]`
- **`Fixtures/TracksPolarsMemory.php`**: Trait to record `$df->estimatedSize()` per benchmark
- **`Polars/`**: Eight benchmark classes using the polars-php extension
- **`PurePhp/`**: Eight matching benchmark classes using native PHP
- **`compare-report.php`**: Custom comparison script — runs phpbench with `--dump-file`, parses the XML output, and prints a side-by-side table

### Benchmark commands (run from `php/`)

```bash
composer bench           # Run all benchmarks (default report)
composer bench:polars    # Polars benchmarks only
composer bench:purephp   # Pure PHP benchmarks only
composer bench:compare   # Run all + print comparison table
```

### Comparison report

`compare-report.php` accepts an optional XML dump file argument:

```bash
# Run benchmarks and show comparison in one step
php benchmarks/compare-report.php

# Use a previously saved dump file
php benchmarks/compare-report.php benchmarks/results.xml
```

Output columns: Benchmark | Polars Time | PHP Time | Speedup | Polars Mem | PHP Mem | Mem Saved

Speedup shows `Nx faster` when Polars wins, `Nx SLOWER` when it doesn't. Memory shows `N% less` or `N% MORE`.

### Data representation

- **Columnar** (`['col' => [values...]]`): DataFrameCreate, Aggregation benchmarks
- **Row-based** (`[['col' => val, ...], ...]`): Filter, Sort, Join, ColumnAccess, CsvWrite pure PHP benchmarks
- **CSV file**: CsvRead benchmarks

### Adding a new benchmark

1. Create `Polars/MyOpBench.php` and `PurePhp/MyOpBench.php` using `#[Bench\...]` attributes
2. Use `DataGenerator::SIZES` for `@ParamProviders` — all sizes including 1M rows
3. Add the `TracksPolarsMemory` trait to the Polars class if a DataFrame is produced

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
