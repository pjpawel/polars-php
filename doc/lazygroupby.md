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

### count

```{php:method} count(): LazyFrame
```

Count rows per group.

### first

```{php:method} first(): LazyFrame
```

Get the first row per group.

### last

```{php:method} last(): LazyFrame
```

Get the last row per group.

### head

```{php:method} head(int $n = 5): LazyFrame
```

Get the first `n` rows per group.

### tail

```{php:method} tail(int $n = 5): LazyFrame
```

Get the last `n` rows per group.

### sum

```{php:method} sum(): LazyFrame
```

Sum all numeric columns per group.

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

### max

```{php:method} max(): LazyFrame
```

Maximum of all columns per group.
