<?php

namespace PolarsPhpBench\PurePhp;

use PhpBench\Attributes as Bench;
use PolarsPhpBench\Fixtures\DataGenerator;

/**
 * Pure PHP column/row access on row-based data.
 */
class ColumnAccessBench
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
    #[Bench\Revs(50)]
    #[Bench\Iterations(5)]
    public function benchSingleColumn(): void
    {
        array_column($this->rows, 'value');
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSizes'])]
    #[Bench\Revs(50)]
    #[Bench\Iterations(5)]
    public function benchSingleRow(): void
    {
        $this->rows[0];
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSizes'])]
    #[Bench\Revs(50)]
    #[Bench\Iterations(5)]
    public function benchMultiColumn(): void
    {
        $ids = array_column($this->rows, 'id');
        $values = array_column($this->rows, 'value');
    }
}
