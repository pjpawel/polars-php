# DataFrame

```{php:class} Polars\DataFrame
```

The `DataFrame` class is the primary data structure in Polars-PHP. It represents a two-dimensional, size-mutable, potentially heterogeneous tabular data structure with labeled columns.

## Constructor

```{php:method} __construct(array $data, bool $byKeys = true)
```

Create a new DataFrame from a PHP array.

:param array $data: Associative array where keys are column names and values are arrays of column data
:param bool $byKeys: Whether to parse data by keys (default: true)
:raises Polars\\Exception: If data cannot be converted to DataFrame

**Example:**

```php
$df = new DataFrame([
    'name' => ['Alice', 'Bob', 'Charlie'],
    'age' => [25, 30, 35],
    'city' => ['NYC', 'LA', 'Chicago']
]);
```

## Static Methods

### fromCsv

```{php:method} static fromCsv(string $path, bool $headerIncluded = true, string $separator = ","): DataFrame
```

Read a DataFrame from a CSV file.

:param string $path: Path to the CSV file
:param bool $headerIncluded: Whether the first row contains column headers (default: true)
:param string $separator: Column separator character (default: ",")
:returns: DataFrame
:raises Polars\\Exception: If file cannot be read or parsed

**Example:**

```php
$df = DataFrame::fromCsv('data.csv');
$df = DataFrame::fromCsv('data.tsv', headerIncluded: true, separator: "\t");
```

## Properties

### getColumns / setColumns

```{php:method} getColumns(): array
```

Get column names as an array of strings.

:returns: string[] - Array of column names

```{php:method} setColumns(array $columns): void
```

Set column names.

:param array $columns: Array of new column names (must match current column count)
:raises Polars\\Exception: If column count doesn't match

**Example:**

```php
$columns = $df->getColumns(); // ['name', 'age', 'city']
$df->setColumns(['first_name', 'years', 'location']);
```

### dtypes

```{php:method} dtypes(): array
```

Get data types of all columns.

:returns: DataType[] - Array of DataType objects

## Dimensions

### height

```{php:method} height(): int
```

Get the number of rows in the DataFrame.

:returns: int - Number of rows

### width

```{php:method} width(): int
```

Get the number of columns in the DataFrame.

:returns: int - Number of columns

### shape

```{php:method} shape(): array
```

Get the shape of the DataFrame as [rows, columns].

:returns: int[] - Array with [height, width]

**Example:**

```php
$df->height(); // 3
$df->width();  // 3
$df->shape();  // [3, 3]
```

## Array Access

DataFrame implements `ArrayAccess`, allowing bracket notation for accessing data.

### offsetExists

```{php:method} offsetExists(mixed $offset): bool
```

Check if an offset (column name or row index) exists.

### offsetGet

```{php:method} offsetGet(mixed $offset): DataFrame
```

Get value at offset. Supports multiple access patterns:

:param mixed $offset: Can be string, int, or array
:returns: DataFrame

**Access patterns:**

| Pattern | Description | Example |
|---------|-------------|---------|
| `string` | Single column | `$df['name']` |
| `int` | Single row (supports negative indexing) | `$df[0]`, `$df[-1]` |
| `array` of strings | Multiple columns | `$df[['name', 'age']]` |
| `array` with int | Specific row from columns | `$df[['name', 'age', 0]]` |

**Example:**

```php
$df['name'];           // Single column as DataFrame
$df[0];                // First row as DataFrame
$df[-1];               // Last row as DataFrame
$df[['name', 'age']];  // Multiple columns
$df[['name', 1]];      // 'name' column, row index 1
```

### offsetSet / offsetUnset

```{php:method} offsetSet(mixed $offset, mixed $value): void
offsetUnset(mixed $offset): void
```

Not supported. Use `withColumn()` or `drop()` methods instead.

## Row Selection

### head

```{php:method} head(int $n = 10): DataFrame
```

Get the first n rows.

:param int $n: Number of rows to return (default: 10)
:returns: DataFrame

### tail

```{php:method} tail(int $n = 10): DataFrame
```

Get the last n rows.

:param int $n: Number of rows to return (default: 10)
:returns: DataFrame

**Example:**

```php
$df->head(5);  // First 5 rows
$df->tail(3);  // Last 3 rows
```

## Aggregations

### count

```{php:method} count(): DataFrame
```

Return the number of non-null elements for each column.

:returns: DataFrame - Single row with counts per column

### max

```{php:method} max(): DataFrame
```

Aggregate columns to their maximum value.

:returns: DataFrame - Single row with max values

### min

```{php:method} min(): DataFrame
```

Aggregate columns to their minimum value.

:returns: DataFrame - Single row with min values

### mean

```{php:method} mean(): DataFrame
```

Aggregate columns to their mean value.

:returns: DataFrame - Single row with mean values

### std

```{php:method} std(int $ddof = 0): DataFrame
```

Aggregate columns to their standard deviation.

:param int $ddof: Delta degrees of freedom (default: 0)
:returns: DataFrame - Single row with std values

**Example:**

```php
$df = new DataFrame(['values' => [1, 2, 3, 4, 5]]);
$df->min()->item();   // 1
$df->max()->item();   // 5
$df->mean()->item();  // 3.0
```

## Selection

### select

```{php:method} select(array $expressions): DataFrame
```

Select columns based on expressions.

:param array $expressions: Array of Expr objects
:returns: DataFrame
:raises Polars\\Exception: If expressions are invalid

**Example:**

```php
use Polars\Expr;

$df = new DataFrame([
    'a' => [1, 2, 3],
    'b' => [4, 5, 6]
]);

// Select with comparison
$result = $df->select([Expr::col('a')->gt(1)]);

// Select with arithmetic
$result = $df->select([Expr::col('a')->add(Expr::col('b'))]);
```

## Utilities

### item

```{php:method} item(): mixed
```

Return the DataFrame as a scalar value. The DataFrame must contain exactly one element (1 row, 1 column).

:returns: mixed - The scalar value (int, float, string, bool, or null)
:raises Polars\\Exception: If DataFrame doesn't have exactly one element

**Example:**

```php
$df = new DataFrame(['x' => [42]]);
$value = $df->item(); // 42
```

### isEmpty

```{php:method} isEmpty(): bool
```

Check if DataFrame is empty.

:returns: bool

### copy

```{php:method} copy(): DataFrame
```

Create a copy of the DataFrame.

:returns: DataFrame

## Output

### writeCsv

```{php:method} writeCsv(string $path, bool $includeHeader = true, string $separator = ","): void
```

Write DataFrame to a CSV file.

:param string $path: Output file path
:param bool $includeHeader: Whether to include column headers
:param string $separator: Column separator character
:raises Polars\\Exception: If file cannot be written

**Example:**

```php
$df->writeCsv('output.csv');
$df->writeCsv('output.tsv', includeHeader: true, separator: "\t");
```

### __toString

```{php:method} __toString(): string
```

Return a formatted string representation of the DataFrame.

**Example:**

```php
echo $df;
// shape: (3, 3)
// ┌─────────┬─────┬─────────┐
// │ name    ┆ age ┆ city    │
// │ ---     ┆ --- ┆ ---     │
// │ str     ┆ i64 ┆ str     │
// ╞═════════╪═════╪═════════╡
// │ Alice   ┆ 25  ┆ NYC     │
// │ Bob     ┆ 30  ┆ LA      │
// │ Charlie ┆ 35  ┆ Chicago │
// └─────────┴─────┴─────────┘
```
