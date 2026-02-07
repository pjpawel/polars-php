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

### readCsv

```{php:method} static readCsv(string $path, bool $hasHeader = true, string $separator = ","): DataFrame
```

Read a DataFrame from a CSV file.

:param string $path: Path to the CSV file
:param bool $hasHeader: Whether the first row contains column headers (default: true)
:param string $separator: Column separator character (default: ",")
:returns: DataFrame
:raises Polars\\Exception: If file cannot be read or parsed

**Example:**

```php
$df = DataFrame::readCsv('data.csv');
$df = DataFrame::readCsv('data.tsv', hasHeader: true, separator: "\t");
```

### readJson

```{php:method} static readJson(string $path): DataFrame
```

Read a DataFrame from a JSON file.

:param string $path: Path to the JSON file
:returns: DataFrame
:raises Polars\\Exception: If file cannot be read or parsed

**Example:**

```php
$df = DataFrame::readJson('data.json');
```

### readNdjson

```{php:method} static readNdjson(string $path): DataFrame
```

Read a DataFrame from a NDJSON (newline-delimited JSON) file.

:param string $path: Path to the NDJSON file
:returns: DataFrame
:raises Polars\\Exception: If file cannot be read or parsed

**Example:**

```php
$df = DataFrame::readNdjson('data.ndjson');
```

### readParquet

```{php:method} static readParquet(string $path): DataFrame
```

Read a DataFrame from a Parquet file.

:param string $path: Path to the Parquet file
:returns: DataFrame
:raises Polars\\Exception: If file cannot be read or parsed

**Example:**

```php
$df = DataFrame::readParquet('data.parquet');
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

## Series Access

### column

```{php:method} column(string $name): Series
```

Get a single column as a Series.

:param string $name: Column name
:returns: Series
:raises Polars\\Exception: If column doesn't exist

**Example:**

```php
$df = new DataFrame([
    'a' => [1, 2, 3],
    'b' => [4, 5, 6],
]);

$series = $df->column('a');
$series->getName(); // 'a'
$series->sum();     // 6
```

### getSeries

```{php:method} getSeries(): array
```

Get all columns as an array of Series.

:returns: Series[] - Array of Series objects

**Example:**

```php
$df = new DataFrame([
    'x' => [1, 2],
    'y' => [3, 4],
]);

$seriesArr = $df->getSeries();
// $seriesArr[0] is Series 'x'
// $seriesArr[1] is Series 'y'
```

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

### sum

```{php:method} sum(): DataFrame
```

Aggregate columns to their sum value.

:returns: DataFrame - Single row with sum values

### median

```{php:method} median(): DataFrame
```

Aggregate columns to their median value.

:returns: DataFrame - Single row with median values

### variance

```{php:method} variance(int $ddof = 0): DataFrame
```

Aggregate columns to their variance.

:param int $ddof: Delta degrees of freedom (default: 0)
:returns: DataFrame - Single row with variance values

### quantile

```{php:method} quantile(float $quantile): DataFrame
```

Aggregate columns to their quantile value.

:param float $quantile: Quantile value between 0 and 1
:returns: DataFrame - Single row with quantile values

### nullCount

```{php:method} nullCount(): DataFrame
```

Get the number of null values per column.

:returns: DataFrame - Single row with null counts

### product

```{php:method} product(): DataFrame
```

Aggregate columns to their product value.

:returns: DataFrame - Single row with product values

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

## Core Manipulation

### sort

```{php:method} sort(string $column, bool $descending = false, bool $nullsLast = true): DataFrame
```

Sort DataFrame by a column.

:param string $column: Column name to sort by
:param bool $descending: Sort in descending order (default: false)
:param bool $nullsLast: Place nulls last (default: true)
:returns: DataFrame

**Example:**

```php
$df->sort('age');
$df->sort('age', descending: true);
```

### drop

```{php:method} drop(array $columns): DataFrame
```

Drop specified columns.

:param array $columns: Column names to drop
:returns: DataFrame

**Example:**

```php
$df->drop(['age', 'city']);
```

### rename

```{php:method} rename(array $existing, array $newNames): DataFrame
```

Rename columns.

:param array $existing: Old column names
:param array $newNames: New column names
:returns: DataFrame

**Example:**

```php
$df->rename(['name', 'age'], ['fullName', 'years']);
```

### filter

```{php:method} filter(Expr $expression): DataFrame
```

Filter rows by expression.

:param Expr $expression: Filter expression
:returns: DataFrame

**Example:**

```php
$df->filter(Expr::col('age')->gt(30));
```

### withColumns

```{php:method} withColumns(array $expressions): DataFrame
```

Add or modify columns using expressions.

:param array $expressions: Array of Expr objects
:returns: DataFrame

**Example:**

```php
$df->withColumns([
    Expr::col('age')->mul(2)->alias('double_age'),
]);
```

### groupBy

```{php:method} groupBy(array $expressions): LazyGroupBy
```

Group by expressions.

:param array $expressions: Array of Expr objects for grouping
:returns: LazyGroupBy

**Example:**

```php
$result = $df->groupBy([Expr::col('city')])->sum()->collect();
```

## Row/Column Manipulation

### unique

```{php:method} unique(?array $subset = null, string $keep = "first"): DataFrame
```

Get unique rows.

:param array|null $subset: Column names to consider for uniqueness (default: all columns)
:param string $keep: Keep strategy - 'first', 'last', 'any', or 'none' (default: 'first')
:returns: DataFrame

### dropNulls

```{php:method} dropNulls(?array $subset = null): DataFrame
```

Drop rows with null values.

:param array|null $subset: Column names to check for nulls (default: all columns)
:returns: DataFrame

### fillNull

```{php:method} fillNull(mixed $value): DataFrame
```

Fill null values with a value or expression.

:param mixed $value: Value to fill nulls with (int, float, string, bool, null, or Expr)
:returns: DataFrame

### fillNan

```{php:method} fillNan(mixed $value): DataFrame
```

Fill NaN values with a value or expression.

:param mixed $value: Value to fill NaN with
:returns: DataFrame

### reverse

```{php:method} reverse(): DataFrame
```

Reverse row order.

:returns: DataFrame

### slice

```{php:method} slice(int $offset, int $length): DataFrame
```

Get a slice of rows.

:param int $offset: Start offset
:param int $length: Number of rows
:returns: DataFrame

### limit

```{php:method} limit(int $n = 10): DataFrame
```

Limit to n rows (alias for head).

:param int $n: Number of rows (default: 10)
:returns: DataFrame

### join

```{php:method} join(DataFrame $other, array $on, string $how = "inner"): DataFrame
```

Join with another DataFrame.

:param DataFrame $other: The right DataFrame
:param array $on: Array of Expr objects for join columns
:param string $how: Join type - 'inner', 'left', 'right', 'full', 'cross' (default: 'inner')
:returns: DataFrame

**Example:**

```php
$result = $df1->join($df2, [Expr::col('id')], how: 'left');
```

### withRowIndex

```{php:method} withRowIndex(string $name = "index", int $offset = 0): DataFrame
```

Add a row index column.

:param string $name: Name of the index column (default: "index")
:param int $offset: Starting offset (default: 0)
:returns: DataFrame

## Export/Row Access

### toArray

```{php:method} toArray(): array
```

Convert DataFrame to a PHP array of associative arrays (rows).

:returns: array - Array of associative arrays

**Example:**

```php
$arr = $df->toArray();
// [['name' => 'Alice', 'age' => 25], ['name' => 'Bob', 'age' => 30], ...]
```

### row

```{php:method} row(int $index): array
```

Get a single row as an associative array. Supports negative indexing.

:param int $index: Row index (negative for counting from end)
:returns: array - Associative array

**Example:**

```php
$row = $df->row(0);   // First row
$row = $df->row(-1);  // Last row
```

### rows

```{php:method} rows(): array
```

Get all rows as array of associative arrays (alias for toArray).

:returns: array

## DataFrame Operations

### vstack

```{php:method} vstack(DataFrame $other): DataFrame
```

Grow this DataFrame vertically by stacking another DataFrame.

:param DataFrame $other: DataFrame to stack
:returns: DataFrame

### hstack

```{php:method} hstack(array $columns): DataFrame
```

Grow this DataFrame horizontally by adding Series columns.

:param array $columns: Array of Series objects
:returns: DataFrame

### equals

```{php:method} equals(DataFrame $other): bool
```

Check if two DataFrames are equal.

:param DataFrame $other: DataFrame to compare with
:returns: bool

### estimatedSize

```{php:method} estimatedSize(): int
```

Get the estimated size in bytes.

:returns: int

### getColumnIndex

```{php:method} getColumnIndex(string $name): int
```

Get the column index by name. Returns -1 if not found.

:param string $name: Column name
:returns: int

### clear

```{php:method} clear(): DataFrame
```

Create an empty copy of the DataFrame (same schema, no rows).

:returns: DataFrame

### rechunk

```{php:method} rechunk(): DataFrame
```

Rechunk the DataFrame into contiguous memory.

:returns: DataFrame

### shrinkToFit

```{php:method} shrinkToFit(): void
```

Shrink memory usage of the DataFrame.

### isDuplicated

```{php:method} isDuplicated(): Series
```

Get a boolean mask of duplicated rows.

:returns: Series - Boolean Series

### isUnique

```{php:method} isUnique(): Series
```

Get a boolean mask of unique rows.

:returns: Series - Boolean Series

## Advanced Operations

### shift

```{php:method} shift(int $n): DataFrame
```

Shift column values by n positions.

:param int $n: Number of positions to shift (positive = down, negative = up)
:returns: DataFrame

### gatherEvery

```{php:method} gatherEvery(int $n, int $offset = 0): DataFrame
```

Take every nth row.

:param int $n: Take every nth row
:param int $offset: Starting offset (default: 0)
:returns: DataFrame

### cast

```{php:method} cast(array $dtypes, bool $strict = false): DataFrame
```

Cast columns to different data types.

:param array $dtypes: Associative array of column name => data type string
:param bool $strict: Use strict casting (default: false)
:returns: DataFrame

**Example:**

```php
$df->cast(['age' => 'float64', 'score' => 'int32']);
```

### unpivot

```{php:method} unpivot(array $on, array $index): DataFrame
```

Unpivot a DataFrame from wide to long format.

:param array $on: Column names to use as values
:param array $index: Column names to use as identifiers
:returns: DataFrame

### explode

```{php:method} explode(array $columns): DataFrame
```

Explode list columns into rows.

:param array $columns: Column names to explode
:returns: DataFrame

## Descriptive Methods

### schema (property)

```{php:method} getSchema(): string
```

Get schema description as string.

:returns: string

### nUnique

```{php:method} nUnique(): DataFrame
```

Get the number of unique values per column.

:returns: DataFrame - Single row with unique counts

### glimpse

```{php:method} glimpse(): string
```

Get a quick summary of the DataFrame.

:returns: string

### describe

```{php:method} describe(): DataFrame
```

Get descriptive statistics (count, mean, std, min, max, median, etc.).

:returns: DataFrame

## Sampling

### sample

```{php:method} sample(int $n, bool $withReplacement = false, bool $shuffle = true, ?int $seed = null): DataFrame
```

Randomly sample n rows.

:param int $n: Number of rows to sample
:param bool $withReplacement: Allow sampling with replacement (default: false)
:param bool $shuffle: Shuffle the result (default: true)
:param int|null $seed: Random seed for reproducibility
:returns: DataFrame

### transpose

```{php:method} transpose(bool $includeHeader = false, string $headerName = "column"): DataFrame
```

Transpose the DataFrame.

:param bool $includeHeader: Include column names as a column (default: false)
:param string $headerName: Name for the header column (default: "column")
:returns: DataFrame

### topK

```{php:method} topK(int $k, string $by): DataFrame
```

Get the top k rows by a column (largest values first).

:param int $k: Number of rows
:param string $by: Column to sort by
:returns: DataFrame

### bottomK

```{php:method} bottomK(int $k, string $by): DataFrame
```

Get the bottom k rows by a column (smallest values first).

:param int $k: Number of rows
:param string $by: Column to sort by
:returns: DataFrame

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

### writeJson

```{php:method} writeJson(string $path): void
```

Write DataFrame to a JSON file.

:param string $path: Output file path
:raises Polars\\Exception: If file cannot be written

**Example:**

```php
$df->writeJson('output.json');
```

### writeNdjson

```{php:method} writeNdjson(string $path): void
```

Write DataFrame to a NDJSON (newline-delimited JSON) file.

:param string $path: Output file path
:raises Polars\\Exception: If file cannot be written

**Example:**

```php
$df->writeNdjson('output.ndjson');
```

### writeParquet

```{php:method} writeParquet(string $path): void
```

Write DataFrame to a Parquet file.

:param string $path: Output file path
:raises Polars\\Exception: If file cannot be written

**Example:**

```php
$df->writeParquet('output.parquet');
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
