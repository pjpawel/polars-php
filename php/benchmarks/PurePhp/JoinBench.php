<?php

namespace PolarsPhpBench\PurePhp;

use PhpBench\Attributes as Bench;
use PolarsPhpBench\Fixtures\DataGenerator;

/**
 * Pure PHP hash-join on row-based data.
 */
class JoinBench
{
    private array $left;
    private array $right;

    public function setUp(array $params): void
    {
        $this->left = DataGenerator::generateRowArray($params['rows']);
        $this->right = DataGenerator::generateJoinRowArray($params['rows']);
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
        $lookup = [];
        foreach ($this->right as $row) {
            $lookup[$row['id']] = $row;
        }
        $result = [];
        foreach ($this->left as $row) {
            if (isset($lookup[$row['id']])) {
                $result[] = array_merge($row, $lookup[$row['id']]);
            }
        }
    }
}
