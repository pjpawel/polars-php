<?php

namespace Tests\Polars;

use PHPUnit\Framework\TestCase;
use Polars\DataFrame;
use Polars\Expr;
use Polars\LazyFrame;
use Polars\LazyGroupBy;

class LazyGroupByTest extends TestCase
{
    private function createDf(): DataFrame
    {
        return new DataFrame([
            'group' => ['a', 'a', 'b', 'b', 'c', 'c'],
            'value' => [1, 2, 3, 4, 5, 6],
            'score' => [10, 20, 30, 40, 50, 60],
        ]);
    }

    public function testGroupByReturnsLazyGroupBy(): void
    {
        $gb = $this->createDf()->lazy()->groupBy([Expr::col('group')]);
        $this->assertInstanceOf(LazyGroupBy::class, $gb);
    }

    // Agg

    public function testAggSum(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->agg([Expr::col('value')->sum()])
            ->sort('group')
            ->collect();
        $this->assertEquals(3, $result->height());
    }

    public function testAggMultiple(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->agg([
                Expr::col('value')->sum(),
                Expr::col('score')->mean(),
            ])
            ->sort('group')
            ->collect();
        $this->assertEquals(3, $result->height());
        $this->assertEquals(3, $result->width());
    }

    // Count

    public function testCount(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->count()
            ->sort('group')
            ->collect();
        $this->assertEquals(3, $result->height());
    }

    // First / Last

    public function testFirst(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->first()
            ->sort('group')
            ->collect();
        $this->assertEquals(3, $result->height());
    }

    public function testLast(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->last()
            ->sort('group')
            ->collect();
        $this->assertEquals(3, $result->height());
    }

    // Head / Tail

    public function testHead(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->head(1)
            ->sort('group')
            ->collect();
        $this->assertEquals(3, $result->height());
    }

    public function testTail(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->tail(1)
            ->sort('group')
            ->collect();
        $this->assertEquals(3, $result->height());
    }

    // Aggregation convenience methods

    public function testSum(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->sum()
            ->sort('group')
            ->collect();
        $this->assertEquals(3, $result->height());
    }

    public function testMean(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->mean()
            ->sort('group')
            ->collect();
        $this->assertEquals(3, $result->height());
    }

    public function testMedian(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->median()
            ->sort('group')
            ->collect();
        $this->assertEquals(3, $result->height());
    }

    public function testMin(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->min()
            ->sort('group')
            ->collect();
        $this->assertEquals(3, $result->height());
    }

    public function testMax(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->max()
            ->sort('group')
            ->collect();
        $this->assertEquals(3, $result->height());
    }

    // Chaining after groupBy

    public function testGroupByThenFilter(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->agg([Expr::col('value')->sum()])
            ->filter(Expr::col('value')->gt(3))
            ->collect();
        $this->assertGreaterThanOrEqual(1, $result->height());
    }

    public function testGroupByThenSelect(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->agg([Expr::col('value')->sum(), Expr::col('score')->mean()])
            ->select([Expr::col('group'), Expr::col('value')])
            ->collect();
        $this->assertEquals(2, $result->width());
    }
}
