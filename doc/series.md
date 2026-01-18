# Series

```{php:class} Polars\Series
```

The `Series` class represents a one-dimensional labeled array capable of holding any data type. It is the building block of a DataFrame - each column in a DataFrame is a Series.

## Constructor

```{php:method} __construct(string $name = "", array $values)
```

Create a new Series from a PHP array.

:param string $name: Name of the Series
:param array $values: Array of values (integers, floats, strings, booleans, or nulls)
:raises Polars\\Exception: If values cannot be converted to Series

**Example:**

```php
$integers = new Series('numbers', [1, 2, 3, 4, 5]);
$floats = new Series('decimals', [1.5, 2.5, 3.5]);
$strings = new Series('names', ['Alice', 'Bob', 'Charlie']);
$booleans = new Series('flags', [true, false, true]);
$empty = new Series('empty', []);
```

## Properties

### getName

```{php:method} getName(): string
```

Get the name of the Series.

:returns: string - Series name

### getDtype

```{php:method} getDtype(): DataType
```

Get the data type of the Series.

:returns: DataType - The data type

### getShape

```{php:method} getShape(): array
```

Get the shape of the Series as [length].

:returns: int[] - Array with single element [length]

**Example:**

```php
$s = new Series('x', [1, 2, 3, 4, 5]);
$s->getName();   // 'x'
$s->getShape();  // [5]
```

## Dimensions

### len

```{php:method} len(): int
```

Get the number of elements in the Series.

:returns: int - Number of elements

### isEmpty

```{php:method} isEmpty(): bool
```

Check if Series is empty.

:returns: bool

### count

```{php:method} count(): int
```

Get the number of elements (Countable interface).

:returns: int

**Example:**

```php
$s = new Series('x', [1, 2, 3]);
$s->len();      // 3
$s->isEmpty();  // false
count($s);      // 3
```

## Array Access

Series implements `ArrayAccess`, allowing bracket notation for accessing elements.

### offsetGet

```{php:method} offsetGet(int $offset): mixed
```

Get value at index. Supports negative indexing.

:param int $offset: Index (supports negative values for indexing from end)
:returns: mixed - Value at index
:raises Polars\\Exception: If index is out of bounds

### offsetExists

```{php:method} offsetExists(int $offset): bool
```

Check if an index exists.

:param int $offset: Index to check
:returns: bool

### offsetSet / offsetUnset

```{php:method} offsetSet(int $offset, mixed $value): void
offsetUnset(int $offset): void
```

Not supported. Series are immutable.

**Example:**

```php
$s = new Series('x', [10, 20, 30, 40, 50]);
$s[0];   // 10
$s[2];   // 30
$s[-1];  // 50 (last element)
$s[-2];  // 40 (second to last)

isset($s[0]);  // true
isset($s[10]); // false
```

## Element Access

### head

```{php:method} head(int $n = 10): Series
```

Get the first n elements.

:param int $n: Number of elements to return (default: 10)
:returns: Series

### tail

```{php:method} tail(int $n = 10): Series
```

Get the last n elements.

:param int $n: Number of elements to return (default: 10)
:returns: Series

### first

```{php:method} first(): mixed
```

Get the first element.

:returns: mixed - First value
:raises Polars\\Exception: If Series is empty

### last

```{php:method} last(): mixed
```

Get the last element.

:returns: mixed - Last value
:raises Polars\\Exception: If Series is empty

### item

```{php:method} item(): mixed
```

Get a single value from the Series. The Series must contain exactly one element.

:returns: mixed - The scalar value
:raises Polars\\Exception: If Series doesn't have exactly one element

### slice

```{php:method} slice(int $offset, int $length): Series
```

Extract a slice of the Series.

:param int $offset: Start index
:param int $length: Number of elements to include
:returns: Series

**Example:**

```php
$s = new Series('x', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
$s->head(3);     // Series [1, 2, 3]
$s->tail(3);     // Series [8, 9, 10]
$s->first();     // 1
$s->last();      // 10
$s->slice(2, 3); // Series [3, 4, 5]

$single = new Series('x', [42]);
$single->item(); // 42
```

## Aggregations

### sum

```{php:method} sum(): mixed
```

Get the sum of all values.

:returns: mixed - Sum value (int or float)

### mean

```{php:method} mean(): float
```

Get the mean of all values.

:returns: float
:raises Polars\\Exception: If mean cannot be computed for this type

### median

```{php:method} median(): float
```

Get the median of all values.

:returns: float
:raises Polars\\Exception: If median cannot be computed for this type

### min

```{php:method} min(): mixed
```

Get the minimum value.

:returns: mixed

### max

```{php:method} max(): mixed
```

Get the maximum value.

:returns: mixed

### std

```{php:method} std(int $ddof = 1): float
```

Get the standard deviation.

:param int $ddof: Delta degrees of freedom (default: 1 for sample std, use 0 for population std)
:returns: float
:raises Polars\\Exception: If std cannot be computed for this type

### variance

```{php:method} variance(int $ddof = 1): float
```

Get the variance.

:param int $ddof: Delta degrees of freedom (default: 1 for sample variance, use 0 for population variance)
:returns: float
:raises Polars\\Exception: If variance cannot be computed for this type

### product

```{php:method} product(): mixed
```

Get the product of all values.

:returns: mixed

**Example:**

```php
$s = new Series('x', [1, 2, 3, 4, 5]);
$s->sum();     // 15
$s->mean();    // 3.0
$s->median();  // 3.0
$s->min();     // 1
$s->max();     // 5
$s->product(); // 120

$s2 = new Series('x', [2, 4, 4, 4, 5, 5, 7, 9]);
$s2->std(0);      // ~2.0 (population std)
$s2->variance(0); // ~4.0 (population variance)
```

## Null Handling

### countNonNull

```{php:method} countNonNull(): int
```

Count non-null values.

:returns: int

### nullCount

```{php:method} nullCount(): int
```

Count null values.

:returns: int

### nUnique

```{php:method} nUnique(): int
```

Count unique values (including null).

:returns: int

**Example:**

```php
$s = new Series('x', [1, 2, null, 4, null]);
$s->countNonNull(); // 3
$s->nullCount();    // 2

$s2 = new Series('x', [1, 2, 2, 3, 3, 3]);
$s2->nUnique(); // 3
```

## Boolean Operations

### isNull

```{php:method} isNull(): Series
```

Check which values are null.

:returns: Series - Boolean Series

### isNotNull

```{php:method} isNotNull(): Series
```

Check which values are not null.

:returns: Series - Boolean Series

### isNan

```{php:method} isNan(): Series
```

Check which values are NaN (for float Series).

:returns: Series - Boolean Series
:raises Polars\\Exception: If Series is not float type

### isNotNan

```{php:method} isNotNan(): Series
```

Check which values are not NaN.

:returns: Series - Boolean Series
:raises Polars\\Exception: If Series is not float type

### any

```{php:method} any(): bool
```

Check if any value is true (for boolean Series).

:returns: bool
:raises Polars\\Exception: If Series is not boolean type

### all

```{php:method} all(): bool
```

Check if all values are true (for boolean Series).

:returns: bool
:raises Polars\\Exception: If Series is not boolean type

**Example:**

```php
$s = new Series('x', [1, null, 3]);
$s->isNull();    // Series [false, true, false]
$s->isNotNull(); // Series [true, false, true]

$bools = new Series('x', [true, false, true]);
$bools->any(); // true
$bools->all(); // false
```

## Comparison Operations

### eq

```{php:method} eq(mixed $other): Series
```

Element-wise equality comparison.

:param mixed $other: Value to compare (int, float, string, bool, or null)
:returns: Series - Boolean Series

### ne

```{php:method} ne(mixed $other): Series
```

Element-wise inequality comparison.

:param mixed $other: Value to compare
:returns: Series - Boolean Series

### lt

```{php:method} lt(mixed $other): Series
```

Element-wise less than comparison.

:param mixed $other: Value to compare
:returns: Series - Boolean Series

### le

```{php:method} le(mixed $other): Series
```

Element-wise less than or equal comparison.

:param mixed $other: Value to compare
:returns: Series - Boolean Series

### gt

```{php:method} gt(mixed $other): Series
```

Element-wise greater than comparison.

:param mixed $other: Value to compare
:returns: Series - Boolean Series

### ge

```{php:method} ge(mixed $other): Series
```

Element-wise greater than or equal comparison.

:param mixed $other: Value to compare
:returns: Series - Boolean Series

**Example:**

```php
$s = new Series('x', [1, 2, 3, 4, 5]);
$s->eq(3);  // Series [false, false, true, false, false]
$s->ne(3);  // Series [true, true, false, true, true]
$s->lt(3);  // Series [true, true, false, false, false]
$s->le(3);  // Series [true, true, true, false, false]
$s->gt(3);  // Series [false, false, false, true, true]
$s->ge(3);  // Series [false, false, true, true, true]
```

## Data Manipulation

### sort

```{php:method} sort(bool $descending = false, bool $nullsLast = true): Series
```

Sort the Series.

:param bool $descending: Sort in descending order (default: false)
:param bool $nullsLast: Place nulls at the end (default: true)
:returns: Series - Sorted Series

### reverse

```{php:method} reverse(): Series
```

Reverse the Series.

:returns: Series

### unique

```{php:method} unique(): Series
```

Get unique values.

:returns: Series

### dropNulls

```{php:method} dropNulls(): Series
```

Remove null values.

:returns: Series

**Example:**

```php
$s = new Series('x', [3, 1, 4, 1, 5, 9, 2, 6]);
$s->sort();                    // [1, 1, 2, 3, 4, 5, 6, 9]
$s->sort(descending: true);    // [9, 6, 5, 4, 3, 2, 1, 1]
$s->reverse();                 // [6, 2, 9, 5, 1, 4, 1, 3]
$s->unique();                  // [1, 2, 3, 4, 5, 6, 9]

$withNulls = new Series('x', [1, null, 2, null, 3]);
$withNulls->dropNulls(); // [1, 2, 3]
```

## Fill Null Methods

### fillNullForward

```{php:method} fillNullForward(): Series
```

Fill null values using forward fill strategy.

:returns: Series

### fillNullBackward

```{php:method} fillNullBackward(): Series
```

Fill null values using backward fill strategy.

:returns: Series

### fillNullMean

```{php:method} fillNullMean(): Series
```

Fill null values with the mean.

:returns: Series

### fillNullZero

```{php:method} fillNullZero(): Series
```

Fill null values with zero.

:returns: Series

**Example:**

```php
$s = new Series('x', [1, null, null, 4, null]);
$s->fillNullForward();  // [1, 1, 1, 4, 4]
$s->fillNullBackward(); // [1, 4, 4, 4, null]
$s->fillNullMean();     // [1, 2.5, 2.5, 4, 2.5]
$s->fillNullZero();     // [1, 0, 0, 4, 0]
```

## Utility Methods

### toArray

```{php:method} toArray(): array
```

Convert Series to PHP array.

:returns: array

### rename

```{php:method} rename(string $name): Series
```

Rename the Series. Returns a new Series (original unchanged).

:param string $name: New name
:returns: Series

### alias

```{php:method} alias(string $name): Series
```

Create an alias for the Series (same as rename).

:param string $name: New name
:returns: Series

### copy

```{php:method} copy(): Series
```

Create a copy of the Series.

:returns: Series

### cast

```{php:method} cast(string $dtype): Series
```

Cast Series to a different data type.

:param string $dtype: Target type. One of: 'int8', 'int16', 'int32', 'int64', 'uint8', 'uint16', 'uint32', 'uint64', 'float32', 'float64', 'bool', 'string'
:returns: Series
:raises Polars\\Exception: If cast fails

### __toString

```{php:method} __toString(): string
```

Return a formatted string representation of the Series.

**Example:**

```php
$s = new Series('x', [1, 2, 3]);
$s->toArray();       // [1, 2, 3]
$s->rename('y');     // Series named 'y'
$s->alias('z');      // Series named 'z'
$s->cast('float64'); // Series with float values

echo $s;
// shape: (3,)
// Series: 'x' [i64]
// [
//     1
//     2
//     3
// ]
```
