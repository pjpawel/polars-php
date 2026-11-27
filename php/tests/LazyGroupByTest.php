<?php

namespace Tests\Polars;

use PHPUnit\Framework\TestCase;
use Polars\DataFrame;
use Polars\Expr;
use Polars\LazyFrame;
use Polars\LazyGroupBy;
use Polars\QuantileMethod;

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

    // All

    public function testAll(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->all()
            ->sort('group')
            ->collect();
        $this->assertEquals(3, $result->height());
        $this->assertEquals(['group', 'value', 'score'], $result->columns);
    }

    // Having

    public function testHavingReturnsLazyGroupBy(): void
    {
        $gb = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->having([Expr::col('value')->sum()->gt(1)]);
        $this->assertInstanceOf(LazyGroupBy::class, $gb);
    }

    public function testHavingFiltersGroups(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->having([Expr::col('value')->sum()->gt(4)])
            ->agg([Expr::col('value')->sum()])
            ->sort('group')
            ->collect();
        $this->assertEquals(2, $result->height());
        $this->assertEquals(['b', 'c'], array_column($result->toArray(), 'group'));
    }

    public function testHavingChained(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->having([Expr::col('value')->sum()->gt(3)])
            ->having([Expr::col('value')->sum()->lt(11)])
            ->agg([Expr::col('value')->sum()])
            ->sort('group')
            ->collect();
        $this->assertEquals(1, $result->height());
    }

    // Len

    public function testLen(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->len()
            ->sort('group')
            ->collect();
        $this->assertEquals(3, $result->height());
        $this->assertContains('len', $result->columns);
        $this->assertEquals(2, $result->row(0)['len']);
    }

    public function testLenWithName(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->len('n')
            ->sort('group')
            ->collect();
        $this->assertEquals(3, $result->height());
        $this->assertContains('n', $result->columns);
        $this->assertEquals(2, $result->row(0)['n']);
    }

    // MapGroups

    public function testMapGroups(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->mapGroups(fn(DataFrame $group) => $group->filter(Expr::col('value')->gt(1)))
            ->sort('group')
            ->collect();
        $this->assertEquals(5, $result->height());
        $this->assertEquals(['group', 'value', 'score'], $result->columns);
    }

    public function testMapGroupsWithSchema(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->mapGroups(
                fn(DataFrame $group) => $group,
                ['group' => 'string', 'value' => 'int64', 'score' => 'int64']
            )
            ->sort('group')
            ->collect();
        $this->assertEquals(6, $result->height());
        $this->assertEquals(['group', 'value', 'score'], $result->columns);
    }

    public function testMapGroupsRequiresCallable(): void
    {
        $this->expectException(\Polars\Exception::class);
        $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->mapGroups('definitely_not_a_callable');
    }

    public function testMapGroupsWithHavingThrows(): void
    {
        $this->expectException(\Polars\Exception::class);
        $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->having([Expr::col('value')->sum()->gt(1)])
            ->mapGroups(fn(DataFrame $group) => $group);
    }

    // NUnique

    public function testNUnique(): void
    {
        $df = new DataFrame([
            'group' => ['a', 'a', 'a', 'b'],
            'value' => [1, 1, 2, 3],
        ]);
        $result = $df->lazy()
            ->groupBy([Expr::col('group')])
            ->nUnique()
            ->sort('group')
            ->collect();
        $this->assertEquals(2, $result->height());
        $this->assertEquals(2, $result->row(0)['value']);
        $this->assertEquals(1, $result->row(1)['value']);
    }

    // Quantile

    public function testQuantile(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->quantile(1)
            ->sort('group')
            ->collect();
        $this->assertEquals(3, $result->height());
        $this->assertEquals(2.0, $result->row(0)['value']);
        $this->assertEquals(20.0, $result->row(0)['score']);
    }

    public function testQuantileWithInterpolation(): void
    {
        $result = $this->createDf()->lazy()
            ->groupBy([Expr::col('group')])
            ->quantile(0.5, QuantileMethod::Linear)
            ->sort('group')
            ->collect();
        $this->assertEquals(3, $result->height());
        $this->assertEquals(1.5, $result->row(0)['value']);
    }
}
