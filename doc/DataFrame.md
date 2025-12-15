# Compatibility list

API of this extension is highly influenced by [Python API](https://docs.pola.rs/api/python/stable/reference/index.html)

### Base notes
Python do not encourage to use getter and setters that are custom in PHP.
All attributes that can be modified in place will have getter and setter methods.


## DataFrame `Polars\DataFrame`
[Reference](https://docs.pola.rs/api/python/stable/reference/dataframe/index.html)

| Python method name         | PHP method name        | Is implemented? | Is tested? | Differences                                                                           |
|----------------------------|------------------------|-----------------|------------|---------------------------------------------------------------------------------------|
| __init__                   | __construct            | [X]             |            | For now constructor will have no parameters creating empty df. Later there will be an |
| **Aggregation group**      |                        | -----           |            |                                                                                       |
| count                      |                        | [X]             |            |                                                                                       |
| max                        |                        | [X]             |            |                                                                                       |
| max_horizontal             |                        | [ ]             |            |                                                                                       |
| mean                       |                        | [X]             |            |                                                                                       |
| mean_horizontal            |                        | [ ]             |            |                                                                                       |
| median                     |                        | [X]             |            |                                                                                       |
| min                        |                        | [X]             |            |                                                                                       |
| min_horizontal             |                        | [ ]             |            |                                                                                       |
| product                    |                        | [ ]             |            |                                                                                       |
| quantile                   |                        | [ ]             |            |                                                                                       |
| std                        |                        | [ ]             |            |                                                                                       |
| sum                        |                        | [ ]             |            |                                                                                       |
| sum_horizontal             |                        | [ ]             |            |                                                                                       |
| var                        |                        | [ ]             |            |                                                                                       |
| **Attributes group**       |                        | -----           |            |                                                                                       |
| columns                    | getColumns, setColumns | [X]             |            |                                                                                       |
| dtypes                     |                        | [X]             |            |                                                                                       | 
| flags                      |                        | [ ]             |            |                                                                                       | 
| height                     |                        | [X]             |            |                                                                                       | 
| schema                     |                        | [ ]             |            |                                                                                       | 
| shape                      |                        | [X]             |            |                                                                                       | 
| width                      |                        | [X]             |            |                                                                                       | 
| **Computation group**      |                        | -----           |            |                                                                                       | 
| fold                       |                        | [ ]             |            |                                                                                       | 
| hash_rows                  |                        | [ ]             |            |                                                                                       |
| **Descriptive group**      |                        | -----           |            |                                                                                       |
| approx_n_unique            |                        | [ ]             |            |                                                                                       |
| describe                   |                        | [ ]             |            |                                                                                       |
| estimated_size             |                        | [ ]             |            |                                                                                       |
| glimpse                    |                        | [ ]             |            |                                                                                       |
| is_duplicated              |                        | [ ]             |            |                                                                                       |
| is_empty                   |                        | [ ]             |            |                                                                                       |
| is_unique                  |                        | [ ]             |            |                                                                                       |
| n_chunks                   |                        | [ ]             |            |                                                                                       |
| n_unique                   |                        | [ ]             |            |                                                                                       |
| null_count                 |                        | [ ]             |            |                                                                                       |
| **Export**                 |                        | [ ]             |            |                                                                                       |
| __array__                  |                        | [ ]             |            |                                                                                       |
| __arrow_c_stream__         |                        | [ ]             |            |                                                                                       |
| __dataframe__              |                        | [ ]             |            |                                                                                       |
| to_arrow                   |                        | [ ]             |            |                                                                                       |
| to_dict                    |                        | [ ]             |            |                                                                                       |
| to_dicts                   |                        | [ ]             |            |                                                                                       |
| to_init_repr               |                        | [ ]             |            |                                                                                       |
| to_jax                     |                        | [ ]             |            |                                                                                       |
| to_numpy                   |                        | [ ]             |            |                                                                                       |
| to_pandas                  |                        | [ ]             |            |                                                                                       |
| to_struct                  |                        | [ ]             |            |                                                                                       |
| to_torch                   |                        | [ ]             |            |                                                                                       |
| **Manipulation/Selection** |                        | -----           |            |                                                                                       |
| __getitem__                |                        | [ ]             |            |                                                                                       |
| __setitem__                |                        | [ ]             |            |                                                                                       |
| bottom_k                   |                        | [ ]             |            |                                                                                       |
| cast                       |                        | [ ]             |            |                                                                                       |
| clear                      |                        | [ ]             |            |                                                                                       |
| clone                      |                        | [X]             |            |                                                                                       |
| drop                       |                        | [ ]             |            |                                                                                       |
| drop_in_place              |                        | [ ]             |            |                                                                                       |
| drop_nans                  |                        | [ ]             |            |                                                                                       |
| drop_nulls                 |                        | [ ]             |            |                                                                                       |
| explode                    |                        | [ ]             |            |                                                                                       |
| extend                     |                        | [ ]             |            |                                                                                       |
| fill_nan                   |                        | [ ]             |            |                                                                                       |
| fill_null                  |                        | [ ]             |            |                                                                                       |
| filter                     |                        | [ ]             |            |                                                                                       |
| gather_every               |                        | [ ]             |            |                                                                                       |
| get_column                 |                        | [ ]             |            |                                                                                       |
| get_column_index           |                        | [ ]             |            |                                                                                       |
| get_columns                |                        | [ ]             |            |                                                                                       |
| group_by                   |                        | [ ]             |            |                                                                                       |
| group_by_dynamic           |                        | [ ]             |            |                                                                                       |
| head                       |                        | [ ]             |            |                                                                                       |
| hstack                     |                        | [ ]             |            |                                                                                       |
| insert_column              |                        | [ ]             |            |                                                                                       |
| interpolate                |                        | [ ]             |            |                                                                                       |
| item                       |                        | [ ]             |            |                                                                                       |
| iter_columns               |                        | [ ]             |            |                                                                                       |
| iter_rows                  |                        | [ ]             |            |                                                                                       |
| iter_slices                |                        | [ ]             |            |                                                                                       |
| join                       |                        | [ ]             |            |                                                                                       |
| join_asof                  |                        | [ ]             |            |                                                                                       |
| join_where                 |                        | [ ]             |            |                                                                                       |
| limit                      |                        | [ ]             |            |                                                                                       |
| match_to_schema            |                        | [ ]             |            |                                                                                       |
| melt                       |                        | [ ]             |            |                                                                                       |
| merge_sorted               |                        | [ ]             |            |                                                                                       |
| partition_by               |                        | [ ]             |            |                                                                                       |
| pipe                       |                        | [ ]             |            |                                                                                       |
| pivot                      |                        | [ ]             |            |                                                                                       |
| rechunk                    |                        | [ ]             |            |                                                                                       |
| remove                     |                        | [ ]             |            |                                                                                       |
| rename                     |                        | [ ]             |            |                                                                                       |
| replace_column             |                        | [ ]             |            |                                                                                       |
| reverse                    |                        | [ ]             |            |                                                                                       |
| rolling                    |                        | [ ]             |            |                                                                                       |
| row                        |                        | [ ]             |            |                                                                                       |
| rows                       |                        | [ ]             |            |                                                                                       |
| rows_by_key                |                        | [ ]             |            |                                                                                       |
| sample                     |                        | [ ]             |            |                                                                                       |
| select                     |                        | [ ]             |            |                                                                                       |
| select_seq                 |                        | [ ]             |            |                                                                                       |
| set_sorted                 |                        | [ ]             |            |                                                                                       |
| shift                      |                        | [ ]             |            |                                                                                       |
| shrink_to_fit              |                        | [ ]             |            |                                                                                       |
| slice                      |                        | [ ]             |            |                                                                                       |
| sort                       |                        | [ ]             |            |                                                                                       |
| sql                        |                        | [ ]             |            |                                                                                       |
| tail                       |                        | [ ]             |            |                                                                                       |
| to_dummies                 |                        | [ ]             |            |                                                                                       |
| to_series                  |                        | [ ]             |            |                                                                                       |
| top_k                      |                        | [ ]             |            |                                                                                       |
| transpose                  |                        | [ ]             |            |                                                                                       |
| unique                     |                        | [ ]             |            |                                                                                       |
| unnest                     |                        | [ ]             |            |                                                                                       |
| unpivot                    |                        | [ ]             |            |                                                                                       |
| unstack                    |                        | [ ]             |            |                                                                                       |
| update                     |                        | [ ]             |            |                                                                                       |
| upsample                   |                        | [ ]             |            |                                                                                       |
| vstack                     |                        | [ ]             |            |                                                                                       |
| with_columns               |                        | [ ]             |            |                                                                                       |
| with_columns_seq           |                        | [ ]             |            |                                                                                       |
| with_row_count             |                        | [ ]             |            |                                                                                       |
| with_row_index             |                        | [ ]             |            |                                                                                       |
| **Miscellaneous**          |                        | [ ]             |            |                                                                                       |

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
