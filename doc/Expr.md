## Expr `Polars\Expr`
[Reference](https://docs.pola.rs/api/python/stable/reference/expressions/index.html)

| Python function name   | PHP function name | Is implemented? | Is tested? | Differences                                   |
|------------------------|-------------------|-----------------|------------|-----------------------------------------------|
| col                    | Expr::col         | [X]             | [ ]        |                                               |
| cols                   | Expr::cols        | [X]             | [ ]        |                                               |
| all                    | Expr::all         | [X]             | [ ]        |                                               |
| abs                    |                   | [ ]             | [ ]        |                                               |
| add                    |                   | [X]             | [ ]        |                                               |
| agg_groups             | -                 | [ ]             | [ ]        | Deprecated - there will be [ ] implementation |
| alias                  |                   | [ ]             | [ ]        |                                               |
| and_                   |                   | [ ]             | [ ]        |                                               |
| any                    |                   | [X]             | [ ]        |                                               |
| append                 |                   | [ ]             | [ ]        |                                               |
| approx_n_unique        |                   | [ ]             | [ ]        |                                               |
| arccos                 |                   | [ ]             | [ ]        |                                               |
| arccosh                |                   | [ ]             | [ ]        |                                               |
| arcsin                 |                   | [ ]             | [ ]        |                                               |
| arcsinh                |                   | [ ]             | [ ]        |                                               |
| arctan                 |                   | [ ]             | [ ]        |                                               |
| arctanh                |                   | [ ]             | [ ]        |                                               |
| arg_max                |                   | [ ]             | [ ]        |                                               |
| arg_min                |                   | [ ]             | [ ]        |                                               |
| arg_sort               |                   | [ ]             | [ ]        |                                               |
| arg_true               |                   | [ ]             | [ ]        |                                               |
| arg_unique             |                   | [ ]             | [ ]        |                                               |
| backward_fill          |                   | [ ]             | [ ]        |                                               |
| bitwise_and            |                   | [ ]             | [ ]        |                                               |
| bitwise_count_ones     |                   | [ ]             | [ ]        |                                               |
| bitwise_count_zeros    |                   | [ ]             | [ ]        |                                               |
| bitwise_leading_ones   |                   | [ ]             | [ ]        |                                               |
| bitwise_leading_zeros  |                   | [ ]             | [ ]        |                                               |
| bitwise_or             |                   | [ ]             | [ ]        |                                               |
| bitwise_trailing_ones  |                   | [ ]             | [ ]        |                                               |
| bitwise_trailing_zeros |                   | [ ]             | [ ]        |                                               |
| bitwise_xor            |                   | [ ]             | [ ]        |                                               |
| bottom_k               |                   | [ ]             | [ ]        |                                               |
| bottom_k_by            |                   | [ ]             | [ ]        |                                               |
| cast                   |                   | [ ]             | [ ]        |                                               |
| cbrt                   |                   | [ ]             | [ ]        |                                               |
| ceil                   |                   | [ ]             | [ ]        |                                               |
| clip                   |                   | [ ]             | [ ]        |                                               |
| cos                    |                   | [ ]             | [ ]        |                                               |
| cosh                   |                   | [ ]             | [ ]        |                                               |
| cot                    |                   | [ ]             | [ ]        |                                               |
| count                  |                   | [X]             | [ ]        |                                               |
| cum_count              |                   | [ ]             | [ ]        |                                               |
| cum_max                |                   | [ ]             | [ ]        |                                               |
| cum_min                |                   | [ ]             | [ ]        |                                               |
| cum_prod               |                   | [ ]             | [ ]        |                                               |
| cum_sum                |                   | [ ]             | [ ]        |                                               |
| cumulative_eval        |                   | [ ]             | [ ]        |                                               |
| cut                    |                   | [ ]             | [ ]        |                                               |
| degrees                |                   | [ ]             | [ ]        |                                               |
| deserialize            |                   | [ ]             | [ ]        |                                               |
| diff                   |                   | [ ]             | [ ]        |                                               |
| dot                    |                   | [ ]             | [ ]        |                                               |
| drop_nans              |                   | [ ]             | [ ]        |                                               |
| drop_nulls             |                   | [ ]             | [ ]        |                                               |
| entropy                |                   | [ ]             | [ ]        |                                               |
| eq                     |                   | [X]             | [ ]        |                                               |
| eq_missing             |                   | [X]             | [ ]        |                                               |
| ewm_mean               |                   | [ ]             | [ ]        |                                               |
| ewm_mean_by            |                   | [ ]             | [ ]        |                                               |
| ewm_std                |                   | [ ]             | [ ]        |                                               |
| ewm_var                |                   | [ ]             | [ ]        |                                               |
| exclude                |                   | [ ]             | [ ]        |                                               |
| exp                    |                   | [ ]             | [ ]        |                                               |
| explode                |                   | [ ]             | [ ]        |                                               |
| extend_constant        |                   | [ ]             | [ ]        |                                               |
| fill_nan               |                   | [ ]             | [ ]        |                                               |
| fill_null              |                   | [ ]             | [ ]        |                                               |
| filter                 |                   | [ ]             | [ ]        |                                               |
| first                  |                   | [X]             | [ ]        |                                               |
| flatten                |                   | [ ]             | [ ]        |                                               |
| floor                  |                   | [ ]             | [ ]        |                                               |
| floordiv               | floorDiv          | [X]             | [ ]        |                                               |
| forward_fill           |                   | [ ]             | [ ]        |                                               |
| from_json              |                   | [ ]             | [ ]        |                                               |
| gather                 |                   | [ ]             | [ ]        |                                               |
| gather_every           |                   | [ ]             | [ ]        |                                               |
| ge                     |                   | [X]             | [ ]        |                                               |
| get                    |                   | [ ]             | [ ]        |                                               |
| gt                     |                   | [X]             | [ ]        |                                               |
| hash                   |                   | [ ]             | [ ]        |                                               |
| has_nulls              | hasNulls          | [X]             | [ ]        |                                               |
| head                   |                   | [ ]             | [ ]        |                                               |
| hist                   |                   | [ ]             | [ ]        |                                               |
| implode                |                   | [ ]             | [ ]        |                                               |
| index_of               |                   | [ ]             | [ ]        |                                               |
| inspect                |                   | [ ]             | [ ]        |                                               |
| interpolate            |                   | [ ]             | [ ]        |                                               |
| interpolate_by         |                   | [ ]             | [ ]        |                                               |
| is_between             |                   | [ ]             | [ ]        |                                               |
| is_close               |                   | [ ]             | [ ]        |                                               |
| is_duplicated          |                   | [ ]             | [ ]        |                                               |
| is_finite              |                   | [ ]             | [ ]        |                                               |
| is_first_distinct      |                   | [ ]             | [ ]        |                                               |
| is_in                  |                   | [ ]             | [ ]        |                                               |
| is_infinite            |                   | [ ]             | [ ]        |                                               |
| is_last_distinct       |                   | [ ]             | [ ]        |                                               |
| is_nan                 |                   | [ ]             | [ ]        |                                               |
| is_[ ]t_nan            |                   | [ ]             | [ ]        |                                               |
| is_[ ]t_null           |                   | [ ]             | [ ]        |                                               |
| is_null                |                   | [ ]             | [ ]        |                                               |
| is_unique              |                   | [ ]             | [ ]        |                                               |
| item                   |                   | [ ]             | [ ]        |                                               |
| kurtosis               |                   | [ ]             | [ ]        |                                               |
| last                   |                   | [X]             | [ ]        |                                               |
| le                     |                   | [X]             | [ ]        |                                               |
| len                    |                   | [X]             | [ ]        |                                               |
| limit                  |                   | [ ]             | [ ]        |                                               |
| log                    |                   | [ ]             | [ ]        |                                               |
| log10                  |                   | [ ]             | [ ]        |                                               |
| log1p                  |                   | [ ]             | [ ]        |                                               |
| lower_bound            |                   | [ ]             | [ ]        |                                               |
| lt                     |                   | [X]             | [ ]        |                                               |
| map_batches            |                   | [ ]             | [ ]        |                                               |
| map_elements           |                   | [ ]             | [ ]        |                                               |
| max                    |                   | [X]             | [ ]        |                                               |
| mean                   |                   | [X]             | [ ]        |                                               |
| median                 |                   | [X]             | [ ]        |                                               |
| min                    |                   | [X]             | [ ]        |                                               |
| mod                    | modulo            | [X]             | [ ]        |                                               |
| mode                   |                   | [ ]             | [ ]        |                                               |
| mul                    |                   | [X]             | [ ]        |                                               |
| nan_max                |                   | [X]             | [ ]        |                                               |
| nan_min                |                   | [X]             | [ ]        |                                               |
| null_count             | nullCount         | [X]             | [ ]        |                                               |
| ne                     |                   | [X]             | [ ]        |                                               |
| neg                    |                   | [X]             | [ ]        |                                               |
| ne_missing             | neq_missing       | [X]             | [ ]        |                                               |
| [ ]t_                  |                   | [ ]             | [ ]        |                                               |
| null_count             |                   | [ ]             | [ ]        |                                               |
| n_unique               | nUnique           | [X]             | [ ]        |                                               |
| or_                    |                   | [ ]             | [ ]        |                                               |
| over                   |                   | [ ]             | [ ]        |                                               |
| pct_change             |                   | [ ]             | [ ]        |                                               |
| peak_max               |                   | [ ]             | [ ]        |                                               |
| peak_min               |                   | [ ]             | [ ]        |                                               |
| pipe                   |                   | [ ]             | [ ]        |                                               |
| pow                    |                   | [X]             | [ ]        |                                               |
| product                |                   | [X]             | [ ]        |                                               |
| qcut                   |                   | [ ]             | [ ]        |                                               |
| quantile               |                   | [ ]             | [ ]        |                                               |
| radians                |                   | [ ]             | [ ]        |                                               |
| rank                   |                   | [ ]             | [ ]        |                                               |
| rechunk                |                   | [ ]             | [ ]        |                                               |
| register_plugin        |                   | [ ]             | [ ]        |                                               |
| reinterpret            |                   | [ ]             | [ ]        |                                               |
| repeat_by              |                   | [ ]             | [ ]        |                                               |
| replace                |                   | [ ]             | [ ]        |                                               |
| replace_strict         |                   | [ ]             | [ ]        |                                               |
| reshape                |                   | [ ]             | [ ]        |                                               |
| reverse                |                   | [ ]             | [ ]        |                                               |
| rle                    |                   | [ ]             | [ ]        |                                               |
| rle_id                 |                   | [ ]             | [ ]        |                                               |
| rolling                |                   | [ ]             | [ ]        |                                               |
| rolling_kurtosis       |                   | [ ]             | [ ]        |                                               |
| rolling_map            |                   | [ ]             | [ ]        |                                               |
| rolling_max            |                   | [ ]             | [ ]        |                                               |
| rolling_max_by         |                   | [ ]             | [ ]        |                                               |
| rolling_mean           |                   | [ ]             | [ ]        |                                               |
| rolling_mean_by        |                   | [ ]             | [ ]        |                                               |
| rolling_median         |                   | [ ]             | [ ]        |                                               |
| rolling_median_by      |                   | [ ]             | [ ]        |                                               |
| rolling_min            |                   | [ ]             | [ ]        |                                               |
| rolling_min_by         |                   | [ ]             | [ ]        |                                               |
| rolling_quantile       |                   | [ ]             | [ ]        |                                               |
| rolling_quantile_by    |                   | [ ]             | [ ]        |                                               |
| rolling_rank           |                   | [ ]             | [ ]        |                                               |
| rolling_rank_by        |                   | [ ]             | [ ]        |                                               |
| rolling_skew           |                   | [ ]             | [ ]        |                                               |
| rolling_std            |                   | [ ]             | [ ]        |                                               |
| rolling_std_by         |                   | [ ]             | [ ]        |                                               |
| rolling_sum            |                   | [ ]             | [ ]        |                                               |
| rolling_sum_by         |                   | [ ]             | [ ]        |                                               |
| rolling_var            |                   | [ ]             | [ ]        |                                               |
| rolling_var_by         |                   | [ ]             | [ ]        |                                               |
| round                  |                   | [ ]             | [ ]        |                                               |
| round_sig_figs         |                   | [ ]             | [ ]        |                                               |
| sample                 |                   | [ ]             | [ ]        |                                               |
| search_sorted          |                   | [ ]             | [ ]        |                                               |
| set_sorted             |                   | [ ]             | [ ]        |                                               |
| shift                  |                   | [ ]             | [ ]        |                                               |
| shrink_dtype           |                   | [ ]             | [ ]        |                                               |
| shuffle                |                   | [ ]             | [ ]        |                                               |
| sign                   |                   | [ ]             | [ ]        |                                               |
| sin                    |                   | [ ]             | [ ]        |                                               |
| sinh                   |                   | [ ]             | [ ]        |                                               |
| skew                   |                   | [ ]             | [ ]        |                                               |
| slice                  |                   | [ ]             | [ ]        |                                               |
| sort                   |                   | [ ]             | [ ]        |                                               |
| sort_by                |                   | [ ]             | [ ]        |                                               |
| sqrt                   |                   | [ ]             | [ ]        |                                               |
| std                    |                   | [X]             | [ ]        |                                               |
| sub                    |                   | [X]             | [ ]        |                                               |
| sum                    |                   | [X]             | [ ]        |                                               |
| tail                   |                   | [ ]             | [ ]        |                                               |
| tan                    |                   | [ ]             | [ ]        |                                               |
| tanh                   |                   | [ ]             | [ ]        |                                               |
| to_physical            |                   | [ ]             | [ ]        |                                               |
| top_k                  |                   | [ ]             | [ ]        |                                               |
| top_k_by               |                   | [ ]             | [ ]        |                                               |
| truediv                |                   | [ ]             | [ ]        |                                               |
| unique                 |                   | [ ]             | [ ]        |                                               |
| unique_counts          |                   | [ ]             | [ ]        |                                               |
| upper_bound            |                   | [ ]             | [ ]        |                                               |
| value_counts           |                   | [ ]             | [ ]        |                                               |
| var                    |                   | [X]             | [ ]        |                                               |
| where                  |                   | [ ]             | [ ]        |                                               |
| xor                    |                   | [X]             | [ ]        |                                               |
