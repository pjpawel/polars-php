<?php

namespace PolarsPhpBench\PurePhp;

use PhpBench\Attributes as Bench;
use PolarsPhpBench\Fixtures\DataGenerator;

/**
 * Pure PHP sort using usort on row-based data.
 */
class SortBench
{
    private array $rows;

    public function setUp(array $params): void
    {
        $this->rows = DataGenerator::generateRowArray($params['rows']);
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
        $rows = $this->rows;
        usort($rows, fn($a, $b) => $a['value'] <=> $b['value']);
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSizes'])]
    #[Bench\Revs(10)]
    #[Bench\Iterations(5)]
    public function benchSortString(): void
    {
        $rows = $this->rows;
        usort($rows, fn($a, $b) => $a['name'] <=> $b['name']);
    }
}
