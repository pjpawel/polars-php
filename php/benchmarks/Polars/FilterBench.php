<?php

namespace PolarsPhpBench\Polars;

use PhpBench\Attributes as Bench;
use Polars\DataFrame;
use Polars\Expr;
use PolarsPhpBench\Fixtures\DataGenerator;

class FilterBench
{
    private DataFrame $df;
    private Expr $expr;

    public function setUp(array $params): void
    {
        $this->df = new DataFrame(DataGenerator::generateArray($params['rows']));
        // 50% selectivity: filter values greater than half the max value
        $threshold = ($params['rows'] - 1) * 1.1 * 0.5;
        $this->expr = Expr::col('value')->gt($threshold);
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
    public function benchFilter(): void
    {
        $this->df->filter($this->expr);
    }
}
