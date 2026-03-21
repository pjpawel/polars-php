<?php

namespace PolarsPhpBench\Polars;

use PhpBench\Attributes as Bench;
use Polars\DataFrame;
use PolarsPhpBench\Fixtures\DataGenerator;
use PolarsPhpBench\Fixtures\TracksPolarsMemory;

class DataFrameCreateBench
{
    use TracksPolarsMemory;

    private array $data;

    public function setUp(array $params): void
    {
        $this->data = DataGenerator::generateArray($params['rows']);
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
    public function benchCreate(): void
    {
        $df = new DataFrame($this->data);
        $this->recordPolarsSize($df);
    }
}
