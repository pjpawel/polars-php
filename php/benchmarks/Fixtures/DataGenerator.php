<?php

namespace PolarsPhpBench\Fixtures;

class DataGenerator
{
    /**
     * Row counts to benchmark at (log scale).
     */
    public const SIZES = [100, 1_000, 10_000, 100_000, 1_000_000];

    /**
     * Generate a columnar associative array suitable for DataFrame constructor.
     * Columns: id (int), value (float), name (string), category (string)
     */
    public static function generateArray(int $rows): array
    {
        $ids = range(1, $rows);
        $values = [];
        $names = [];
        $categories = ['A', 'B', 'C', 'D', 'E'];
        $catData = [];

        for ($i = 0; $i < $rows; $i++) {
            $values[] = $i * 1.1;
            $names[] = 'name_' . $i;
            $catData[] = $categories[$i % 5];
        }

        return [
            'id' => $ids,
            'value' => $values,
            'name' => $names,
            'category' => $catData,
        ];
    }

    /**
     * Generate a row-based array of associative arrays.
     * Each element: ['id' => int, 'value' => float, 'name' => string, 'category' => string]
     */
    public static function generateRowArray(int $rows): array
    {
        $categories = ['A', 'B', 'C', 'D', 'E'];
        $result = [];

        for ($i = 0; $i < $rows; $i++) {
            $result[] = [
                'id' => $i + 1,
                'value' => $i * 1.1,
                'name' => 'name_' . $i,
                'category' => $categories[$i % 5],
            ];
        }

        return $result;
    }

    /**
     * Generate a CSV file with the given number of rows. Returns the file path.
     */
    public static function generateCsv(int $rows): string
    {
        $path = sys_get_temp_dir() . '/polars_bench_' . $rows . '.csv';
        if (file_exists($path)) {
            return $path;
        }

        $fp = fopen($path, 'w');
        fputcsv($fp, ['id', 'value', 'name', 'category']);
        $categories = ['A', 'B', 'C', 'D', 'E'];
        for ($i = 0; $i < $rows; $i++) {
            fputcsv($fp, [$i + 1, $i * 1.1, 'name_' . $i, $categories[$i % 5]]);
        }
        fclose($fp);

        return $path;
    }

    /**
     * Generate a columnar array for join benchmarks (right side).
     * Columns: id (int, shuffled), score (int, random)
     */
    public static function generateJoinArray(int $rows): array
    {
        srand(42);
        $ids = range(1, $rows);
        shuffle($ids);
        $scores = [];
        for ($i = 0; $i < $rows; $i++) {
            $scores[] = rand(0, 100);
        }

        return [
            'id' => $ids,
            'score' => $scores,
        ];
    }

    /**
     * Generate a row-based array for join benchmarks (right side).
     * Each element: ['id' => int, 'score' => int]
     */
    public static function generateJoinRowArray(int $rows): array
    {
        srand(42);
        $ids = range(1, $rows);
        shuffle($ids);
        $result = [];
        for ($i = 0; $i < $rows; $i++) {
            $result[] = [
                'id' => $ids[$i],
                'score' => rand(0, 100),
            ];
        }

        return $result;
    }
}
