# Expr

```{php:class} Polars\Expr
```

The `Expr` class represents an expression that can be used to select, filter, and transform DataFrame columns. Expressions are lazily evaluated and can be composed to build complex operations.

## Constructor

```{php:method} __construct(mixed $value)
```

Create a literal expression from a PHP value.

:param mixed $value: int, float, string, bool, or null
:raises Polars\\Exception: If value type is not supported

**Example:**

```php
$literal = new Expr(42);
$literal = new Expr("hello");
$literal = new Expr(3.14);
```

## Static Methods

### col

```{php:method} static col(string $name): Expr
```

Reference a column by name.

:param string $name: Column name
:returns: Expr

**Example:**

```php
$expr = Expr::col('age');
```

### cols

```{php:method} static cols(array $names): Expr
```

Reference multiple columns by name.

:param array $names: Array of column names
:returns: Expr

**Example:**

```php
$expr = Expr::cols(['name', 'age', 'city']);
```

### all

```{php:method} static all(): Expr
```

Select all columns.

:returns: Expr

**Example:**

```php
$expr = Expr::all();
```

## Aggregation Methods

All aggregation methods return a new `Expr` and can be chained.

### any

```{php:method} any(bool $ignoreNulls = true): Expr
```

Return whether any value is true.

:param bool $ignoreNulls: Whether to ignore null values (default: true)
:returns: Expr

### count

```{php:method} count(): Expr
```

Count the number of values.

:returns: Expr

### first

```{php:method} first(): Expr
```

Get the first value.

:returns: Expr

### last

```{php:method} last(): Expr
```

Get the last value.

:returns: Expr

### len

```{php:method} len(): Expr
```

Get the length of the column.

:returns: Expr

### max

```{php:method} max(): Expr
```

Get the maximum value.

:returns: Expr

### mean

```{php:method} mean(): Expr
```

Get the mean value.

:returns: Expr

### median

```{php:method} median(): Expr
```

Get the median value.

:returns: Expr

### min

```{php:method} min(): Expr
```

Get the minimum value.

:returns: Expr

### nUnique

```{php:method} nUnique(): Expr
```

Count unique values.

:returns: Expr

### nanMax

```{php:method} nanMax(): Expr
```

Get the maximum value, propagating NaN.

:returns: Expr

### nanMin

```{php:method} nanMin(): Expr
```

Get the minimum value, propagating NaN.

:returns: Expr

### nullCount

```{php:method} nullCount(): Expr
```

Count null values.

:returns: Expr

### product

```{php:method} product(): Expr
```

Get the product of all values.

:returns: Expr

### std

```{php:method} std(int $ddof = 1): Expr
```

Get the standard deviation.

:param int $ddof: Delta degrees of freedom (default: 1)
:returns: Expr

### sum

```{php:method} sum(): Expr
```

Get the sum of all values.

:returns: Expr

### variance

```{php:method} variance(int $ddof = 1): Expr
```

Get the variance.

:param int $ddof: Delta degrees of freedom (default: 1)
:returns: Expr

**Example:**

```php
use Polars\Expr;

$df = new DataFrame(['values' => [1, 2, 3, 4, 5]]);

$result = $df->select([
    Expr::col('values')->sum(),
    Expr::col('values')->mean(),
    Expr::col('values')->max(),
]);
```

## Comparison Operators

All comparison methods accept `int`, `float`, `string`, `bool`, `null`, or another `Expr` object.

### eq

```{php:method} eq(mixed $other): Expr
```

Equal to.

:param mixed $other: Value to compare
:returns: Expr - Boolean expression

### eqMissing

```{php:method} eqMissing(mixed $other): Expr
```

Equal to, treating null as a value.

:param mixed $other: Value to compare
:returns: Expr - Boolean expression

### ne

```{php:method} ne(mixed $other): Expr
```

Not equal to.

:param mixed $other: Value to compare
:returns: Expr - Boolean expression

### neqMissing

```{php:method} neqMissing(mixed $other): Expr
```

Not equal to, treating null as a value.

:param mixed $other: Value to compare
:returns: Expr - Boolean expression

### gt

```{php:method} gt(mixed $other): Expr
```

Greater than.

:param mixed $other: Value to compare
:returns: Expr - Boolean expression

### ge

```{php:method} ge(mixed $other): Expr
```

Greater than or equal to.

:param mixed $other: Value to compare
:returns: Expr - Boolean expression

### lt

```{php:method} lt(mixed $other): Expr
```

Less than.

:param mixed $other: Value to compare
:returns: Expr - Boolean expression

### le

```{php:method} le(mixed $other): Expr
```

Less than or equal to.

:param mixed $other: Value to compare
:returns: Expr - Boolean expression

**Example:**

```php
use Polars\Expr;

// Filter rows where age > 25
$expr = Expr::col('age')->gt(25);
$result = $df->select([$expr]);

// Compare columns
$expr = Expr::col('a')->gt(Expr::col('b'));
```

## Arithmetic Operators

All arithmetic methods accept `int`, `float`, `string`, `bool`, `null`, or another `Expr` object.

### add

```{php:method} add(mixed $other): Expr
```

Addition.

:param mixed $other: Value to add
:returns: Expr

### sub

```{php:method} sub(mixed $other): Expr
```

Subtraction.

:param mixed $other: Value to subtract
:returns: Expr

### mul

```{php:method} mul(mixed $other): Expr
```

Multiplication.

:param mixed $other: Value to multiply by
:returns: Expr

### div

```{php:method} div(mixed $other): Expr
```

Division.

:param mixed $other: Value to divide by
:returns: Expr

### floorDiv

```{php:method} floorDiv(mixed $other): Expr
```

Floor division (integer division).

:param mixed $other: Value to divide by
:returns: Expr

### modulo

```{php:method} modulo(mixed $other): Expr
```

Modulo (remainder).

:param mixed $other: Divisor
:returns: Expr

### pow

```{php:method} pow(mixed $other): Expr
```

Power/exponentiation.

:param mixed $other: Exponent
:returns: Expr

### neg

```{php:method} neg(): Expr
```

Negation (multiply by -1).

:returns: Expr

### xxor

```{php:method} xxor(mixed $other): Expr
```

Exclusive OR (XOR).

:param mixed $other: Value to XOR with
:returns: Expr

**Example:**

```php
use Polars\Expr;

$df = new DataFrame([
    'a' => [1, 2, 3],
    'b' => [4, 5, 6]
]);

// Add columns
$sum = Expr::col('a')->add(Expr::col('b'));

// Multiply by constant
$doubled = Expr::col('a')->mul(2);

// Complex expression
$result = Expr::col('a')->add(Expr::col('b'))->div(2);
```

## Boolean Methods

### hasNulls

```{php:method} hasNulls(): Expr
```

Check if the column has any null values.

:returns: Expr - Boolean expression

### isBetween

```{php:method} isBetween(mixed $lowerBound, mixed $upperBound, ClosedInterval $closed): Expr
```

Check if values are between lower and upper bounds.

:param mixed $lowerBound: Lower bound value
:param mixed $upperBound: Upper bound value
:param ClosedInterval $closed: Which bounds are inclusive
:returns: Expr - Boolean expression

**Example:**

```php
use Polars\Expr;
use Polars\ClosedInterval;

$df = new DataFrame(['age' => [18, 25, 30, 45, 60]]);

// Check if age is between 20 and 40 (inclusive)
$expr = Expr::col('age')->isBetween(20, 40, ClosedInterval::Both);
$result = $df->select([$expr]);
```

## Method Chaining

Expressions can be chained to build complex operations:

```php
use Polars\Expr;

// Chain multiple operations
$expr = Expr::col('price')
    ->mul(Expr::col('quantity'))
    ->sum();

// Filter and aggregate
$expr = Expr::col('value')
    ->gt(0)
    ->sum();
```
