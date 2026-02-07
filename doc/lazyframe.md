# LazyFrame

```{php:class} Polars\LazyFrame
```

The `LazyFrame` class represents a lazy computation graph. Operations on a LazyFrame are not executed immediately — instead, they build a query plan that is optimized and executed when `collect()` is called.

Create a LazyFrame via `DataFrame::lazy()`, or by scanning a file with `LazyFrame::scanCsv()`, `LazyFrame::scanNdjson()`, or `LazyFrame::scanParquet()`.

## Scan Methods (Static Constructors)

### scanCsv

```{php:method} static scanCsv(string $path, bool $hasHeader = true, string $separator = ","): LazyFrame
```

Scan a CSV file into a LazyFrame. The file is not fully read into memory — instead, a query plan is created that reads data on demand.

:param string $path: Path to the CSV file
:param bool $hasHeader: Whether the first row contains column headers (default: true)
:param string $separator: Column separator character (default: ",")
:returns: LazyFrame
:raises Polars\\Exception: If file cannot be scanned

**Example:**

```php
$lf = LazyFrame::scanCsv('data.csv');
$df = $lf->filter(Expr::col('age')->gt(30))->collect();
```

### scanNdjson

```{php:method} static scanNdjson(string $path): LazyFrame
```

Scan a NDJSON (newline-delimited JSON) file into a LazyFrame.

:param string $path: Path to the NDJSON file
:returns: LazyFrame
:raises Polars\\Exception: If file cannot be scanned

**Example:**

```php
$lf = LazyFrame::scanNdjson('data.ndjson');
$df = $lf->select([Expr::col('name'), Expr::col('age')])->collect();
```

### scanParquet

```{php:method} static scanParquet(string $path): LazyFrame
```

Scan a Parquet file into a LazyFrame. Parquet scanning is highly efficient due to columnar format and predicate pushdown.

:param string $path: Path to the Parquet file
:returns: LazyFrame
:raises Polars\\Exception: If file cannot be scanned

**Example:**

```php
$lf = LazyFrame::scanParquet('data.parquet');
$df = $lf->filter(Expr::col('salary')->gt(50000))->collect();
```

## Core Methods

### collect

```{php:method} collect(): DataFrame
```

Execute the lazy query and return a materialized DataFrame.

:returns: DataFrame
:raises Polars\\Exception: If the query plan fails to execute

**Example:**

```php
$df = new DataFrame(['a' => [1, 2, 3]]);
$result = $df->lazy()
    ->filter(Expr::col('a')->gt(1))
    ->collect();
```

### select

```{php:method} select(array $expressions): LazyFrame
```

Select columns by expression.

:param array $expressions: Array of `Polars\Expr` objects
:returns: LazyFrame

### filter

```{php:method} filter(Expr $expression): LazyFrame
```

Filter rows by a boolean expression.

:param Expr $expression: Boolean expression to filter by
:returns: LazyFrame

### withColumns

```{php:method} withColumns(array $expressions): LazyFrame
```

Add or overwrite columns using expressions.

:param array $expressions: Array of `Polars\Expr` objects
:returns: LazyFrame

### groupBy

```{php:method} groupBy(array $expressions): LazyGroupBy
```

Group by one or more expressions. Returns a `LazyGroupBy` object.

:param array $expressions: Array of `Polars\Expr` objects to group by
:returns: LazyGroupBy

### sort

```{php:method} sort(string $column, bool $descending = false, bool $nullsLast = true): LazyFrame
```

Sort by a column.

:param string $column: Column name to sort by
:param bool $descending: Sort in descending order (default: false)
:param bool $nullsLast: Place null values last (default: true)
:returns: LazyFrame

## Attributes

### columns (getter)

```{php:method} getColumns(): array
```

Get column names.

:returns: string[]

### dtypes (getter)

```{php:method} getDtypes(): array
```

Get data types of all columns.

:returns: DataType[]

### width

```{php:method} width(): int
```

Get the number of columns.

### schema

```{php:method} schema(): string
```

Get the schema description as a string.

## Row Operations

### head

```{php:method} head(int $n = 10): LazyFrame
```

Get the first `n` rows.

### tail

```{php:method} tail(int $n = 10): LazyFrame
```

Get the last `n` rows.

### first

```{php:method} first(): LazyFrame
```

Get the first row.

### last

```{php:method} last(): LazyFrame
```

Get the last row.

### slice

```{php:method} slice(int $offset, int $length): LazyFrame
```

Get a slice of rows.

### limit

```{php:method} limit(int $n = 10): LazyFrame
```

Limit to `n` rows (alias for `head`).

## Aggregations

### count

```{php:method} count(): LazyFrame
```

Count non-null elements per column.

### sum / mean / median / min / max

```{php:method} sum(): LazyFrame
mean(): LazyFrame
median(): LazyFrame
min(): LazyFrame
max(): LazyFrame
```

Aggregate all columns to their respective values.

### std

```{php:method} std(int $ddof = 0): LazyFrame
```

Standard deviation with configurable degrees of freedom.

### variance

```{php:method} variance(int $ddof = 0): LazyFrame
```

Variance with configurable degrees of freedom.

### quantile

```{php:method} quantile(float $quantile): LazyFrame
```

Quantile aggregation (uses nearest method).

### nullCount

```{php:method} nullCount(): LazyFrame
```

Count null values per column.

## Column Manipulation

### drop

```{php:method} drop(array $columns): LazyFrame
```

Drop columns by name.

:param array $columns: string[] column names to drop

### rename

```{php:method} rename(array $existing, array $newNames): LazyFrame
```

Rename columns.

:param array $existing: Old column names
:param array $newNames: New column names

### unique

```{php:method} unique(?array $subset = null, string $keep = 'first'): LazyFrame
```

Remove duplicate rows.

:param array|null $subset: Column names to consider (null = all columns)
:param string $keep: Strategy: 'first', 'last', 'any', or 'none'

## Null Handling

### dropNulls

```{php:method} dropNulls(?array $subset = null): LazyFrame
```

Drop rows containing null values.

:param array|null $subset: Column names to check (null = all columns)

### fillNull

```{php:method} fillNull(mixed $value): LazyFrame
```

Fill null values with a literal or expression.

### fillNan

```{php:method} fillNan(mixed $value): LazyFrame
```

Fill NaN values with a literal or expression.

## Join

### join

```{php:method} join(LazyFrame $other, array $on, string $how = 'inner'): LazyFrame
```

Join with another LazyFrame.

:param LazyFrame $other: Right side of the join
:param array $on: Array of `Polars\Expr` objects for join columns
:param string $how: Join type: 'inner', 'left', 'right', 'full', 'cross'

**Example:**

```php
$result = $df1->lazy()
    ->join($df2->lazy(), [Expr::col('key')], how: 'inner')
    ->collect();
```

## Miscellaneous

### withRowIndex

```{php:method} withRowIndex(string $name = "index", int $offset = 0): LazyFrame
```

Add a row index column.

:param string $name: Name of the index column (default: "index")
:param int $offset: Starting offset (default: 0)
:returns: LazyFrame

### reverse

```{php:method} reverse(): LazyFrame
```

Reverse the row order.

### explain

```{php:method} explain(bool $optimized = true): string
```

Return the query plan as a string.

:param bool $optimized: Show optimized plan (default: true)

### cache

```{php:method} cache(): LazyFrame
```

Cache intermediate results.

## Sink Methods

Sink methods execute the lazy query plan and write results directly to a file. They return a DataFrame with the result.

### sinkCsv

```{php:method} sinkCsv(string $path, bool $includeHeader = true, string $separator = ","): DataFrame
```

Sink the LazyFrame to a CSV file.

:param string $path: Output file path
:param bool $includeHeader: Whether to include column headers (default: true)
:param string $separator: Column separator character (default: ",")
:returns: DataFrame
:raises Polars\\Exception: If sink operation fails

**Example:**

```php
$df = new DataFrame(['a' => [1, 2, 3], 'b' => [4, 5, 6]]);
$df->lazy()
    ->filter(Expr::col('a')->gt(1))
    ->sinkCsv('output.csv');
```

### sinkParquet

```{php:method} sinkParquet(string $path): DataFrame
```

Sink the LazyFrame to a Parquet file.

:param string $path: Output file path
:returns: DataFrame
:raises Polars\\Exception: If sink operation fails

**Example:**

```php
$df->lazy()->sinkParquet('output.parquet');
```

### sinkNdjson

```{php:method} sinkNdjson(string $path): DataFrame
```

Sink the LazyFrame to a NDJSON (newline-delimited JSON) file.

:param string $path: Output file path
:returns: DataFrame
:raises Polars\\Exception: If sink operation fails

**Example:**

```php
$df->lazy()->sinkNdjson('output.ndjson');
```

### __toString

Returns the unoptimized query plan as a string.
