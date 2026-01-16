# DataType

```{php:class} Polars\DataType
```

The `DataType` class represents the data type of a column in a DataFrame. DataType objects are returned by the `DataFrame::dtypes()` method.

## Overview

Polars supports various data types for efficient data storage and operations:

| Type | Description |
|------|-------------|
| `Boolean` | True/false values |
| `Int8`, `Int16`, `Int32`, `Int64` | Signed integers |
| `UInt8`, `UInt16`, `UInt32`, `UInt64` | Unsigned integers |
| `Float32`, `Float64` | Floating point numbers |
| `String` | UTF-8 encoded strings |
| `Null` | Null/missing values |

## Constructor

```{php:method} __construct()
```

DataType objects are typically not constructed directly. They are returned by DataFrame methods.

## Usage

```php
use Polars\DataFrame;

$df = new DataFrame([
    'name' => ['Alice', 'Bob'],
    'age' => [25, 30],
    'score' => [95.5, 87.3],
    'active' => [true, false]
]);

$types = $df->dtypes();
// Returns array of DataType objects for each column
```

## Type Inference

When creating a DataFrame from a PHP array, Polars automatically infers the data type based on the values:

| PHP Type | Polars Type |
|----------|-------------|
| `bool` | Boolean |
| `int` | Int64 |
| `float` | Float64 |
| `string` | String |
| `null` | Null (or nullable variant) |

**Example:**

```php
$df = new DataFrame([
    'integers' => [1, 2, 3],           // Int64
    'floats' => [1.5, 2.5, 3.5],       // Float64
    'strings' => ['a', 'b', 'c'],      // String
    'booleans' => [true, false, true], // Boolean
    'nullable' => [1, null, 3],        // Int64 (nullable)
]);
```
