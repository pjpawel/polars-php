# Compatibility list

API of this extension is highly influenced by [Python API](https://docs.pola.rs/api/python/stable/reference/index.html)

### Base notes
Python do not encourage to use getter and setters that are custom in PHP.
All attributes that can be modified in place will have getter and setter methods.


## DataFrame `Polars\DataFrame`
[Reference](https://docs.pola.rs/api/python/stable/reference/dataframe/index.html)

| Python method name         | PHP method name        | Is implemented? | Is tested? | Differences                                                                           |
|----------------------------|------------------------|-----------------|------------|---------------------------------------------------------------------------------------|
| __init__                   | __construct            | [X]             | [X]        | Constructor accepts array with column data keyed by column name                       |
| **Aggregation group**      |                        | -----           | -----      |                                                                                       |
| count                      | count                  | [X]             | [X]        |                                                                                       |
| max                        | max                    | [X]             | [X]        |                                                                                       |
| max_horizontal             |                        | [ ]             | [ ]        |                                                                                       |
| mean                       | mean                   | [X]             | [X]        |                                                                                       |
| mean_horizontal            |                        | [ ]             | [ ]        |                                                                                       |
| median                     |                        | [ ]             | [ ]        | Not implemented on DataFrame, use Expr::col()->median()                               |
| min                        | min                    | [X]             | [X]        |                                                                                       |
| min_horizontal             |                        | [ ]             | [ ]        |                                                                                       |
| product                    |                        | [ ]             | [ ]        |                                                                                       |
| quantile                   |                        | [ ]             | [ ]        |                                                                                       |
| std                        | std                    | [X]             | [X]        |                                                                                       |
| sum                        |                        | [ ]             | [ ]        | Not implemented on DataFrame, use Expr::col()->sum()                                  |
| sum_horizontal             |                        | [ ]             | [ ]        |                                                                                       |
| var                        |                        | [ ]             | [ ]        |                                                                                       |
| **Attributes group**       |                        | -----           | -----      |                                                                                       |
| columns                    | getColumns, setColumns | [X]             | [X]        |                                                                                       |
| dtypes                     | dtypes                 | [X]             | [ ]        |                                                                                       |
| flags                      |                        | [ ]             | [ ]        |                                                                                       |
| height                     | height                 | [X]             | [X]        |                                                                                       |
| schema                     |                        | [ ]             | [ ]        |                                                                                       |
| shape                      | shape                  | [X]             | [X]        |                                                                                       |
| width                      | width                  | [X]             | [X]        |                                                                                       |
| **Computation group**      |                        | -----           | -----      |                                                                                       |
| fold                       |                        | [ ]             | [ ]        |                                                                                       |
| hash_rows                  |                        | [ ]             | [ ]        |                                                                                       |
| **Descriptive group**      |                        | -----           | -----      |                                                                                       |
| approx_n_unique            |                        | [ ]             | [ ]        |                                                                                       |
| describe                   |                        | [ ]             | [ ]        |                                                                                       |
| estimated_size             |                        | [ ]             | [ ]        |                                                                                       |
| glimpse                    |                        | [ ]             | [ ]        |                                                                                       |
| is_duplicated              |                        | [ ]             | [ ]        |                                                                                       |
| is_empty                   | isEmpty                | [X]             | [X]        |                                                                                       |
| is_unique                  |                        | [ ]             | [ ]        |                                                                                       |
| n_chunks                   |                        | [ ]             | [ ]        |                                                                                       |
| n_unique                   |                        | [ ]             | [ ]        |                                                                                       |
| null_count                 |                        | [ ]             | [ ]        |                                                                                       |
| **Export**                 |                        | -----           | -----      |                                                                                       |
| __array__                  |                        | [ ]             | [ ]        |                                                                                       |
| __arrow_c_stream__         |                        | [ ]             | [ ]        |                                                                                       |
| __dataframe__              |                        | [ ]             | [ ]        |                                                                                       |
| to_arrow                   |                        | [ ]             | [ ]        |                                                                                       |
| to_dict                    |                        | [ ]             | [ ]        |                                                                                       |
| to_dicts                   |                        | [ ]             | [ ]        |                                                                                       |
| to_init_repr               |                        | [ ]             | [ ]        |                                                                                       |
| to_jax                     |                        | [ ]             | [ ]        |                                                                                       |
| to_numpy                   |                        | [ ]             | [ ]        |                                                                                       |
| to_pandas                  |                        | [ ]             | [ ]        |                                                                                       |
| to_struct                  |                        | [ ]             | [ ]        |                                                                                       |
| to_torch                   |                        | [ ]             | [ ]        |                                                                                       |
| **Manipulation/Selection** |                        | -----           | -----      |                                                                                       |
| __getitem__                | offsetGet              | [X]             | [X]        | Supports string, int, array of strings, array with row index                          |
| __setitem__                | offsetSet              | [X]             | [ ]        | Raises exception - use withColumn() instead                                           |
| bottom_k                   |                        | [ ]             | [ ]        |                                                                                       |
| cast                       |                        | [ ]             | [ ]        |                                                                                       |
| clear                      |                        | [ ]             | [ ]        |                                                                                       |
| clone                      | copy                   | [X]             | [X]        |                                                                                       |
| drop                       |                        | [ ]             | [ ]        |                                                                                       |
| drop_in_place              |                        | [ ]             | [ ]        |                                                                                       |
| drop_nans                  |                        | [ ]             | [ ]        |                                                                                       |
| drop_nulls                 |                        | [ ]             | [ ]        |                                                                                       |
| explode                    |                        | [ ]             | [ ]        |                                                                                       |
| extend                     |                        | [ ]             | [ ]        |                                                                                       |
| fill_nan                   |                        | [ ]             | [ ]        |                                                                                       |
| fill_null                  |                        | [ ]             | [ ]        |                                                                                       |
| filter                     |                        | [ ]             | [ ]        |                                                                                       |
| gather_every               |                        | [ ]             | [ ]        |                                                                                       |
| get_column                 | column                 | [X]             | [X]        | Returns Series object                                                                 |
| get_column_index           |                        | [ ]             | [ ]        |                                                                                       |
| get_columns                | getSeries              | [X]             | [X]        | Returns array of Series objects                                                       |
| group_by                   |                        | [ ]             | [ ]        |                                                                                       |
| group_by_dynamic           |                        | [ ]             | [ ]        |                                                                                       |
| head                       | head                   | [X]             | [ ]        |                                                                                       |
| hstack                     |                        | [ ]             | [ ]        |                                                                                       |
| insert_column              |                        | [ ]             | [ ]        |                                                                                       |
| interpolate                |                        | [ ]             | [ ]        |                                                                                       |
| item                       | item                   | [X]             | [X]        |                                                                                       |
| iter_columns               |                        | [ ]             | [ ]        |                                                                                       |
| iter_rows                  |                        | [ ]             | [ ]        |                                                                                       |
| iter_slices                |                        | [ ]             | [ ]        |                                                                                       |
| join                       |                        | [ ]             | [ ]        |                                                                                       |
| join_asof                  |                        | [ ]             | [ ]        |                                                                                       |
| join_where                 |                        | [ ]             | [ ]        |                                                                                       |
| limit                      |                        | [ ]             | [ ]        |                                                                                       |
| match_to_schema            |                        | [ ]             | [ ]        |                                                                                       |
| melt                       |                        | [ ]             | [ ]        |                                                                                       |
| merge_sorted               |                        | [ ]             | [ ]        |                                                                                       |
| partition_by               |                        | [ ]             | [ ]        |                                                                                       |
| pipe                       |                        | [ ]             | [ ]        |                                                                                       |
| pivot                      |                        | [ ]             | [ ]        |                                                                                       |
| rechunk                    |                        | [ ]             | [ ]        |                                                                                       |
| remove                     |                        | [ ]             | [ ]        |                                                                                       |
| rename                     |                        | [ ]             | [ ]        |                                                                                       |
| replace_column             |                        | [ ]             | [ ]        |                                                                                       |
| reverse                    |                        | [ ]             | [ ]        |                                                                                       |
| rolling                    |                        | [ ]             | [ ]        |                                                                                       |
| row                        |                        | [ ]             | [ ]        |                                                                                       |
| rows                       |                        | [ ]             | [ ]        |                                                                                       |
| rows_by_key                |                        | [ ]             | [ ]        |                                                                                       |
| sample                     |                        | [ ]             | [ ]        |                                                                                       |
| select                     | select                 | [X]             | [X]        | Accepts array of Expr objects                                                         |
| select_seq                 |                        | [ ]             | [ ]        |                                                                                       |
| set_sorted                 |                        | [ ]             | [ ]        |                                                                                       |
| shift                      |                        | [ ]             | [ ]        |                                                                                       |
| shrink_to_fit              |                        | [ ]             | [ ]        |                                                                                       |
| slice                      |                        | [ ]             | [ ]        |                                                                                       |
| sort                       |                        | [ ]             | [ ]        |                                                                                       |
| sql                        |                        | [ ]             | [ ]        |                                                                                       |
| tail                       | tail                   | [X]             | [ ]        |                                                                                       |
| to_dummies                 |                        | [ ]             | [ ]        |                                                                                       |
| to_series                  |                        | [ ]             | [ ]        |                                                                                       |
| top_k                      |                        | [ ]             | [ ]        |                                                                                       |
| transpose                  |                        | [ ]             | [ ]        |                                                                                       |
| unique                     |                        | [ ]             | [ ]        |                                                                                       |
| unnest                     |                        | [ ]             | [ ]        |                                                                                       |
| unpivot                    |                        | [ ]             | [ ]        |                                                                                       |
| unstack                    |                        | [ ]             | [ ]        |                                                                                       |
| update                     |                        | [ ]             | [ ]        |                                                                                       |
| upsample                   |                        | [ ]             | [ ]        |                                                                                       |
| vstack                     |                        | [ ]             | [ ]        |                                                                                       |
| with_columns               |                        | [ ]             | [ ]        |                                                                                       |
| with_columns_seq           |                        | [ ]             | [ ]        |                                                                                       |
| with_row_count             |                        | [ ]             | [ ]        |                                                                                       |
| with_row_index             |                        | [ ]             | [ ]        |                                                                                       |
| **I/O**                    |                        | -----           | -----      |                                                                                       |
| read_csv (function)        | fromCsv                | [X]             | [ ]        | Static method on DataFrame class                                                      |
| write_csv                  | writeCsv               | [X]             | [ ]        |                                                                                       |
| **Miscellaneous**          |                        | -----           | -----      |                                                                                       |
| __str__                    | __toString             | [X]             | [ ]        |                                                                                       |

## GroupBy `Polars\DataFrame\GroupBy`
[Reference](https://docs.pola.rs/api/python/stable/reference/dataframe/group_by.html)

| Python method name | PHP method name | Is implemented? | Is tested? | Differences |
|--------------------|-----------------|-----------------|------------|-------------|
| __iter__           |                 | [ ]             |            |             |
| agg                |                 | [ ]             |            |             |
| all                |                 | [ ]             |            |             |
| count              |                 | [ ]             |            |             |
| first              |                 | [ ]             |            |             |
| head               |                 | [ ]             |            |             |
| last               |                 | [ ]             |            |             |
| len                |                 | [ ]             |            |             |
| map_groups         |                 | [ ]             |            |             |
| max                |                 | [ ]             |            |             |
| mean               |                 | [ ]             |            |             |
| median             |                 | [ ]             |            |             |
| min                |                 | [ ]             |            |             |
| n_unique           |                 | [ ]             |            |             |
| quantile           |                 | [ ]             |            |             |
| sum                |                 | [ ]             |            |             |
| tail               |                 | [ ]             |            |             |
