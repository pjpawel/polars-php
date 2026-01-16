# ClosedInterval

```{php:enum} Polars\ClosedInterval
```

The `ClosedInterval` enum specifies which bounds are included in an interval check, used with the `Expr::isBetween()` method.

## Cases

### Both

```{php:case} Both
```

Both lower and upper bounds are inclusive: `[lower, upper]`

The condition matches values where: `lower <= value <= upper`

### Left

```{php:case} Left
```

Only the lower bound is inclusive: `[lower, upper)`

The condition matches values where: `lower <= value < upper`

### Right

```{php:case} Right
```

Only the upper bound is inclusive: `(lower, upper]`

The condition matches values where: `lower < value <= upper`

### None

```{php:case} None
```

Neither bound is inclusive: `(lower, upper)`

The condition matches values where: `lower < value < upper`

## Usage

```php
use Polars\DataFrame;
use Polars\Expr;
use Polars\ClosedInterval;

$df = new DataFrame([
    'value' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
]);

// Both bounds inclusive [3, 7]: matches 3, 4, 5, 6, 7
$expr = Expr::col('value')->isBetween(3, 7, ClosedInterval::Both);

// Left bound inclusive [3, 7): matches 3, 4, 5, 6
$expr = Expr::col('value')->isBetween(3, 7, ClosedInterval::Left);

// Right bound inclusive (3, 7]: matches 4, 5, 6, 7
$expr = Expr::col('value')->isBetween(3, 7, ClosedInterval::Right);

// Neither bound inclusive (3, 7): matches 4, 5, 6
$expr = Expr::col('value')->isBetween(3, 7, ClosedInterval::None);
```

## Visual Representation

```
Number line: 1  2  3  4  5  6  7  8  9  10

Both   [3,7]:       [===========]
Left   [3,7):       [========)
Right  (3,7]:          (========]
None   (3,7):          (=====)
```

## Example with DataFrame

```php
use Polars\DataFrame;
use Polars\Expr;
use Polars\ClosedInterval;

$df = new DataFrame([
    'temperature' => [15.0, 20.0, 22.5, 25.0, 28.0, 30.0, 35.0]
]);

// Find comfortable temperatures between 20 and 28 (inclusive)
$comfortable = Expr::col('temperature')
    ->isBetween(20.0, 28.0, ClosedInterval::Both);

$result = $df->select([$comfortable]);
// Returns boolean column: [false, true, true, true, true, false, false]
```
