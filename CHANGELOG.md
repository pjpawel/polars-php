# Changelog

All notable changes to this project will be documented in this file.

## 0.4.0
- [Feature] Add missing aggregation methods for Series class: `argMax()`, `argMin()`, `nanMax()`, `nanMin()`, `maxBy()`, `minBy()`, `mode()`, `implode()`
- [Feature] Add `Series::getFlags()` method to retrieve sorting and optimization flags
- [Feature] Benchmark suite comparing polars-php vs pure PHP (PHPBench) for DataFrame creation, CSV I/O, aggregations, filtering, sorting, joins, and column access
