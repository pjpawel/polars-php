<?php

namespace PolarsPhpBench\Polars;

use PhpBench\Attributes as Bench;
use Polars\DataFrame;
use Polars\Expr;
use PolarsPhpBench\Fixtures\DataGenerator;

class JoinBench
{
    private DataFrame $df1;
    private DataFrame $df2;

    public function setUp(array $params): void
    {
        $this->df1 = new DataFrame(DataGenerator::generateArray($params['rows']));
        $this->df2 = new DataFrame(DataGenerator::generateJoinArray($params['rows']));
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
    public function benchJoinInner(): void
    {
        $this->df1->join($this->df2, [Expr::col('id')], how: 'inner');
    }
}
