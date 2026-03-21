<?php

namespace PolarsPhpBench\Polars;

use PhpBench\Attributes as Bench;
use Polars\DataFrame;
use PolarsPhpBench\Fixtures\DataGenerator;

class ColumnAccessBench
{
    private DataFrame $df;

    public function setUp(array $params): void
    {
        $this->df = new DataFrame(DataGenerator::generateArray($params['rows']));
    }

    public function provideSizes(): \Generator
    {
        foreach (DataGenerator::SIZES as $size) {
            yield sprintf('%d rows', $size) => ['rows' => $size];
        }
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSizes'])]
    #[Bench\Revs(50)]
    #[Bench\Iterations(5)]
    public function benchSingleColumn(): void
    {
        $this->df['value'];
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSizes'])]
    #[Bench\Revs(50)]
    #[Bench\Iterations(5)]
    public function benchSingleRow(): void
    {
        $this->df[0];
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSizes'])]
    #[Bench\Revs(50)]
    #[Bench\Iterations(5)]
    public function benchMultiColumn(): void
    {
        $this->df[['id', 'value']];
    }
}
