<?php

namespace PolarsPhpBench\Polars;

use PhpBench\Attributes as Bench;
use Polars\DataFrame;
use PolarsPhpBench\Fixtures\DataGenerator;

class AggregationBench
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
    public function benchSum(): void
    {
        $this->df->sum();
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSizes'])]
    #[Bench\Revs(50)]
    #[Bench\Iterations(5)]
    public function benchMean(): void
    {
        $this->df->mean();
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSizes'])]
    #[Bench\Revs(50)]
    #[Bench\Iterations(5)]
    public function benchMin(): void
    {
        $this->df->min();
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSizes'])]
    #[Bench\Revs(50)]
    #[Bench\Iterations(5)]
    public function benchMax(): void
    {
        $this->df->max();
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSizes'])]
    #[Bench\Revs(50)]
    #[Bench\Iterations(5)]
    public function benchStd(): void
    {
        $this->df->std();
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSizes'])]
    #[Bench\Revs(50)]
    #[Bench\Iterations(5)]
    public function benchMedian(): void
    {
        $this->df->median();
    }
}
