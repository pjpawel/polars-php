<?php

namespace PolarsPhpBench\PurePhp;

use PhpBench\Attributes as Bench;
use PolarsPhpBench\Fixtures\DataGenerator;

/**
 * Pure PHP aggregations on columnar arrays.
 * Uses the most idiomatic PHP approach for each operation.
 */
class AggregationBench
{
    private array $values;

    public function setUp(array $params): void
    {
        $data = DataGenerator::generateArray($params['rows']);
        $this->values = $data['value'];
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
        array_sum($this->values);
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSizes'])]
    #[Bench\Revs(50)]
    #[Bench\Iterations(5)]
    public function benchMean(): void
    {
        array_sum($this->values) / count($this->values);
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSizes'])]
    #[Bench\Revs(50)]
    #[Bench\Iterations(5)]
    public function benchMin(): void
    {
        min($this->values);
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSizes'])]
    #[Bench\Revs(50)]
    #[Bench\Iterations(5)]
    public function benchMax(): void
    {
        max($this->values);
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSizes'])]
    #[Bench\Revs(50)]
    #[Bench\Iterations(5)]
    public function benchStd(): void
    {
        $count = count($this->values);
        $mean = array_sum($this->values) / $count;
        $sumSquaredDiff = 0.0;
        foreach ($this->values as $v) {
            $diff = $v - $mean;
            $sumSquaredDiff += $diff * $diff;
        }
        sqrt($sumSquaredDiff / $count);
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSizes'])]
    #[Bench\Revs(50)]
    #[Bench\Iterations(5)]
    public function benchMedian(): void
    {
        $sorted = $this->values;
        sort($sorted);
        $count = count($sorted);
        $mid = intdiv($count, 2);
        if ($count % 2 === 0) {
            ($sorted[$mid - 1] + $sorted[$mid]) / 2;
        } else {
            $sorted[$mid];
        }
    }
}
