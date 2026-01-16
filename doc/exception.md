# Exception

```{php:class} Polars\Exception
```

The `Exception` class is the base exception type for all errors thrown by the Polars-PHP extension. It extends PHP's built-in `\Exception` class.

## Hierarchy

```
\Exception
└── Polars\Exception
```

## When Exceptions Are Thrown

Polars exceptions are thrown in various situations:

### DataFrame Operations

- Creating a DataFrame with invalid data
- Accessing non-existent columns
- Row index out of bounds
- Invalid column count when setting column names
- File I/O errors when reading/writing CSV

### Expression Operations

- Invalid value types passed to expression methods
- Passing non-Expr objects where Expr is expected

## Handling Exceptions

```php
use Polars\DataFrame;
use Polars\Exception;

try {
    $df = new DataFrame([
        'col1' => [1, 2, 3]
    ]);

    // This will throw - column doesn't exist
    $result = $df['nonexistent'];

} catch (Exception $e) {
    echo "Polars error: " . $e->getMessage();
}
```

## Common Exception Messages

| Situation | Example Message |
|-----------|-----------------|
| Column not found | `Column 'name' not found: ...` |
| Row out of bounds | `Row index 10 out of bounds for DataFrame with 5 rows` |
| Invalid column count | `Failed to set DataFrame column names: ...` |
| Invalid type | `Unsupported type 'array' for column 'col1'` |
| File not found | `Failed to read CSV: ...` |
| Invalid expression | `Passed object is not of class Polars\Expr` |

## Example

```php
use Polars\DataFrame;
use Polars\Expr;
use Polars\Exception;

try {
    $df = DataFrame::fromCsv('nonexistent.csv');
} catch (Exception $e) {
    // Handle file not found
    echo "Could not load file: " . $e->getMessage();
}

try {
    $df = new DataFrame(['col' => [1, 2, 3]]);
    $df->setColumns(['a', 'b']); // Wrong count
} catch (Exception $e) {
    // Handle column count mismatch
    echo "Column error: " . $e->getMessage();
}

try {
    $df = new DataFrame(['col' => [1, 2, 3]]);
    // Trying to use non-Expr in select
    $df->select(['invalid']);
} catch (Exception $e) {
    // Handle invalid expression
    echo "Expression error: " . $e->getMessage();
}
```
