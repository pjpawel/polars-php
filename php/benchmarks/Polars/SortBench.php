<?php

namespace PolarsPhpBench\Polars;

use PhpBench\Attributes as Bench;
use Polars\DataFrame;
use PolarsPhpBench\Fixtures\DataGenerator;

class SortBench
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
    #[Bench\Revs(10)]
    #[Bench\Iterations(5)]
    public function benchSortNumeric(): void
    {
        $this->df->sort('value');
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSizes'])]
    #[Bench\Revs(10)]
    #[Bench\Iterations(5)]
    public function benchSortString(): void
    {
        $this->df->sort('name');
    }
}
