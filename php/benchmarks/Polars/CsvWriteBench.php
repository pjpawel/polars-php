<?php

namespace PolarsPhpBench\Polars;

use PhpBench\Attributes as Bench;
use Polars\DataFrame;
use PolarsPhpBench\Fixtures\DataGenerator;

class CsvWriteBench
{
    private DataFrame $df;
    private string $outPath;

    public function setUp(array $params): void
    {
        $this->df = new DataFrame(DataGenerator::generateArray($params['rows']));
        $this->outPath = sys_get_temp_dir() . '/polars_bench_write_' . $params['rows'] . '.csv';
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
        $this->df->writeCsv($this->outPath, true);
    }
}
