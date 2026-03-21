<?php

namespace PolarsPhpBench\Polars;

use PhpBench\Attributes as Bench;
use Polars\DataFrame;
use PolarsPhpBench\Fixtures\DataGenerator;
use PolarsPhpBench\Fixtures\TracksPolarsMemory;

class CsvReadBench
{
    use TracksPolarsMemory;

    private string $csvPath;

    public function setUp(array $params): void
    {
        $this->csvPath = DataGenerator::generateCsv($params['rows']);
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
    #[Bench\Warmup(1)]
    public function benchReadCsv(): void
    {
        $df = DataFrame::readCsv($this->csvPath);
        $this->recordPolarsSize($df);
    }
}
