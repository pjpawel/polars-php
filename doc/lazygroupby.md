# LazyGroupBy

```{php:class} Polars\LazyGroupBy
```

The `LazyGroupBy` class represents a grouped lazy computation. It is created by calling `LazyFrame::groupBy()` and provides methods to aggregate grouped data.

## Methods

### agg

```{php:method} agg(array $expressions): LazyFrame
```

Apply custom aggregation expressions.

:param array $expressions: Array of `Polars\Expr` objects
:returns: LazyFrame

**Example:**

```php
$result = $df->lazy()
    ->groupBy([Expr::col('group')])
    ->agg([
        Expr::col('value')->sum(),
        Expr::col('score')->mean(),
    ])
    ->collect();
```

### all

```{php:method} all(): LazyFrame
```

Aggregate all values of each group into lists.

**Example:**

```php
$result = $df->lazy()
    ->groupBy([Expr::col('group')])
    ->all()
    ->collect();
```

### count

```{php:method} count(): LazyFrame
```

Count rows per group.

### first

```{php:method} first(): LazyFrame
```

Get the first row per group.

### having

```{php:method} having(array $predicates): LazyGroupBy
```

Filter groups with predicates that are evaluated on the aggregated groups. Returns a new `LazyGroupBy`, so it can be chained before `agg()`; chained predicates are combined with AND.

:param array $predicates: Array of `Polars\Expr` objects, typically aggregations (e.g. `Expr::col('value')->sum()->gt(10)`)
:returns: LazyGroupBy

**Example:**

```php
$result = $df->lazy()
    ->groupBy([Expr::col('group')])
    ->having([Expr::col('value')->sum()->gt(4)])
    ->agg([Expr::col('value')->sum()])
    ->collect();
```

### head

```{php:method} head(int $n = 5): LazyFrame
```

Get the first `n` rows per group.

### last

```{php:method} last(): LazyFrame
```

Get the last row per group.

### len

```{php:method} len(?string $name = null): LazyFrame
```

Return the number of rows in each group. The resulting column is named `len`, or `$name` when given.

:param string|null $name: Assign a name to the resulting column
:returns: LazyFrame

**Example:**

```php
$result = $df->lazy()
    ->groupBy([Expr::col('group')])
    ->len('n')
    ->collect();
```

### mapGroups

```{php:method} mapGroups(callable $function, ?array $schema = null): LazyFrame
```

Apply a custom function over each group as a new DataFrame. The callable receives a `Polars\DataFrame` per group and must return a `Polars\DataFrame`.

```{warning}
This method is much slower than the native expressions API: it materializes the whole `DataFrame` of each group and is opaque to the query optimizer. Prefer expressions when possible.
```

:param callable $function: Function applied to each group, e.g. `fn(DataFrame $group) => $group->filter(...)`
:param array|null $schema: Output schema as column name => dtype (dtype name string such as `'int64'`, or a `Polars\DataType` object). If null, the input schema is assumed unchanged
:returns: LazyFrame

**Example:**

```php
$result = $df->lazy()
    ->groupBy([Expr::col('group')])
    ->mapGroups(fn(DataFrame $group) => $group->filter(Expr::col('value')->gt(1)))
    ->collect();
```

### max

```{php:method} max(): LazyFrame
```

Maximum of all columns per group.

### mean

```{php:method} mean(): LazyFrame
```

Mean of all numeric columns per group.

### median

```{php:method} median(): LazyFrame
```

Median of all numeric columns per group.

### min

```{php:method} min(): LazyFrame
```

Minimum of all columns per group.

### nUnique

```{php:method} nUnique(): LazyFrame
```

Count the unique values per group.

### quantile

```{php:method} quantile(int|float|string|bool|null|Expr $quantile, QuantileMethod $interpolation = QuantileMethod::Nearest): LazyFrame
```

Compute the quantile per group.

:param int|float|string|bool|null|Expr $quantile: Quantile between 0.0 and 1.0
:param QuantileMethod $interpolation: Interpolation method (`QuantileMethod::Nearest` by default)
:returns: LazyFrame

**Example:**

```php
$result = $df->lazy()
    ->groupBy([Expr::col('group')])
    ->quantile(0.5, QuantileMethod::Linear)
    ->collect();
```

### sum

```{php:method} sum(): LazyFrame
```

Sum all numeric columns per group.

### tail

```{php:method} tail(int $n = 5): LazyFrame
```

Get the last `n` rows per group.
