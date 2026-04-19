# QuantileMethod

```{php:enum} Polars\QuantileMethod
```

The `QuantileMethod` enum specifies the interpolation method used when computing quantiles with the `Expr::quantile()` method.

## Cases

### Nearest

```{php:case} Nearest
```

Select the nearest value. This is the default method.

### Lower

```{php:case} Lower
```

Select the lower value.

### Higher

```{php:case} Higher
```

Select the higher value.

### Midpoint

```{php:case} Midpoint
```

Use the midpoint between the two nearest values.

### Linear

```{php:case} Linear
```

Use linear interpolation between the two nearest values.

### Equiprobable

```{php:case} Equiprobable
```

Use equiprobable interpolation.

## Usage

```php
use Polars\DataFrame;
use Polars\Expr;
use Polars\QuantileMethod;

$df = new DataFrame([
    'value' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
]);

// Compute the median (0.5 quantile) using nearest interpolation
$result = $df->select([Expr::col('value')->quantile(0.5, QuantileMethod::Nearest)]);

// Compute the 75th percentile using linear interpolation
$result = $df->select([Expr::col('value')->quantile(0.75, QuantileMethod::Linear)]);

// Compute the 25th percentile using lower bound
$result = $df->select([Expr::col('value')->quantile(0.25, QuantileMethod::Lower)]);
```
