<?php

namespace PolarsPhpBench\PurePhp;

use PhpBench\Attributes as Bench;
use PolarsPhpBench\Fixtures\DataGenerator;

/**
 * Pure PHP CSV write using fputcsv on row-based data.
 */
class CsvWriteBench
{
    private array $rows;
    private string $outPath;

    public function setUp(array $params): void
    {
        $this->rows = DataGenerator::generateRowArray($params['rows']);
        $this->outPath = sys_get_temp_dir() . '/purephp_bench_write_' . $params['rows'] . '.csv';
    }

    public function provideSizes(): \Generator
    {
        foreach (DataGenerator::SIZES as $size) {
            yield sprintf('%d rows', $size) => ['rows' => $size];
        }
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSizes'])]
    #[Bench\Revs(10)]
    #[Bench\Iterations(5)]
    public function benchWriteCsv(): void
    {
        $fp = fopen($this->outPath, 'w');
        fputcsv($fp, ['id', 'value', 'name', 'category']);
        foreach ($this->rows as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
    }
}
