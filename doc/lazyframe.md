# LazyFrame

```{php:class} Polars\LazyFrame
```

The `LazyFrame` class represents a lazy computation graph. Operations on a LazyFrame are not executed immediately — instead, they build a query plan that is optimized and executed when `collect()` is called.

LazyFrame cannot be constructed directly. Use `DataFrame::lazy()` to create one.

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

### __toString

Returns the unoptimized query plan as a string.
