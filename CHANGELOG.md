# Changelog

All notable changes to this project will be documented in this file.

## 0.3.0

### Added

- **DataFrame IO**: `readJson()`, `readNdjson()`, `readParquet()` static methods for reading data from JSON, NDJSON, and Parquet files
- **DataFrame IO**: `writeJson()`, `writeNdjson()`, `writeParquet()` instance methods for writing data to JSON, NDJSON, and Parquet files
- **LazyFrame scan**: `scanCsv()`, `scanNdjson()`, `scanParquet()` static methods for lazy file scanning
- **LazyFrame sink**: `sinkCsv()`, `sinkParquet()`, `sinkNdjson()` instance methods for writing lazy query results directly to files

### Changed

- Renamed `DataFrame::fromCsv()` to `DataFrame::readCsv()` for consistency with the Polars Python API
- Renamed `fromCsv` parameter `headerIncluded` to `hasHeader` for consistency with the Polars Python API
