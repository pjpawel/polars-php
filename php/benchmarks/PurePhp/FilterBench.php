<?php

namespace PolarsPhpBench\PurePhp;

use PhpBench\Attributes as Bench;
use PolarsPhpBench\Fixtures\DataGenerator;

/**
 * Pure PHP filter using array_filter on row-based data.
 * 50% selectivity: filters rows where value > threshold (half of max value).
 */
class FilterBench
{
    private array $rows;
    private float $threshold;

    public function setUp(array $params): void
    {
        $this->rows = DataGenerator::generateRowArray($params['rows']);
        $this->threshold = ($params['rows'] - 1) * 1.1 * 0.5;
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
        $threshold = $this->threshold;
        array_filter($this->rows, fn($row) => $row['value'] > $threshold);
    }
}
