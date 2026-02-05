<?php

namespace Tests\Polars;

use Exception;
use PHPUnit\Framework\TestCase;
use Polars\DataFrame;
use Polars\Expr;
use Polars\LazyFrame;
use Polars\LazyGroupBy;

class LazyFrameTest extends TestCase
{
    private function createDf(): DataFrame
    {
        return new DataFrame([
            'name' => ['Alice', 'Bob', 'Charlie', 'Diana'],
            'age' => [25, 30, 35, 28],
            'salary' => [50000, 60000, 75000, 55000],
        ]);
    }

    // Construction and Collection

    public function testLazy(): void
    {
        $lf = $this->createDf()->lazy();
        $this->assertInstanceOf(LazyFrame::class, $lf);
    }

    public function testCollect(): void
    {
        $df = $this->createDf();
        $result = $df->lazy()->collect();
        $this->assertInstanceOf(DataFrame::class, $result);
        $this->assertEquals(4, $result->height());
        $this->assertEquals(3, $result->width());
    }

    // Attributes

    public function testGetColumns(): void
    {
        $lf = $this->createDf()->lazy();
        $this->assertEquals(['name', 'age', 'salary'], $lf->getColumns());
    }

    public function testWidth(): void
    {
        $lf = $this->createDf()->lazy();
        $this->assertEquals(3, $lf->width());
    }

    public function testSchema(): void
    {
        $lf = $this->createDf()->lazy();
        $schema = $lf->schema();
        $this->assertIsString($schema);
        $this->assertNotEmpty($schema);
    }

    // Select

    public function testSelect(): void
    {
        $result = $this->createDf()->lazy()
            ->select([Expr::col('name'), Expr::col('age')])
            ->collect();
        $this->assertEquals(2, $result->width());
        $this->assertEquals(['name', 'age'], $result->getColumns());
        $this->assertEquals(4, $result->height());
    }

    public function testSelectSingleColumn(): void
    {
        $result = $this->createDf()->lazy()
            ->select([Expr::col('age')])
            ->collect();
        $this->assertEquals(1, $result->width());
        $this->assertEquals(['age'], $result->getColumns());
    }

    // Filter

    public function testFilter(): void
    {
        $result = $this->createDf()->lazy()
            ->filter(Expr::col('age')->gt(28))
            ->collect();
        $this->assertEquals(2, $result->height());
    }

    public function testFilterEq(): void
    {
        $result = $this->createDf()->lazy()
            ->filter(Expr::col('name')->eq('Alice'))
            ->collect();
        $this->assertEquals(1, $result->height());
    }

    // WithColumns

    public function testWithColumns(): void
    {
        $result = $this->createDf()->lazy()
            ->withColumns([Expr::col('salary')->mul(2)])
            ->collect();
        $this->assertEquals(3, $result->width());
        $this->assertEquals(4, $result->height());
    }

    // GroupBy

    public function testGroupBy(): void
    {
        $df = new DataFrame([
            'group' => ['a', 'a', 'b', 'b'],
            'value' => [1, 2, 3, 4],
        ]);
        $gb = $df->lazy()->groupBy([Expr::col('group')]);
        $this->assertInstanceOf(LazyGroupBy::class, $gb);
    }

    public function testGroupByAgg(): void
    {
        $df = new DataFrame([
            'group' => ['a', 'a', 'b', 'b'],
            'value' => [1, 2, 3, 4],
        ]);
        $result = $df->lazy()
            ->groupBy([Expr::col('group')])
            ->agg([Expr::col('value')->sum()])
            ->sort('group')
            ->collect();
        $this->assertEquals(2, $result->height());
    }

    // Sort

    public function testSort(): void
    {
        $result = $this->createDf()->lazy()
            ->sort('age')
            ->collect();
        $this->assertEquals(4, $result->height());
    }

    public function testSortDescending(): void
    {
        $result = $this->createDf()->lazy()
            ->sort('age', descending: true)
            ->collect();
        $this->assertEquals(4, $result->height());
    }

    // Row Operations

    public function testHead(): void
    {
        $result = $this->createDf()->lazy()->head(2)->collect();
        $this->assertEquals(2, $result->height());
    }

    public function testTail(): void
    {
        $result = $this->createDf()->lazy()->tail(2)->collect();
        $this->assertEquals(2, $result->height());
    }

    public function testFirst(): void
    {
        $result = $this->createDf()->lazy()->first()->collect();
        $this->assertEquals(1, $result->height());
    }

    public function testLast(): void
    {
        $result = $this->createDf()->lazy()->last()->collect();
        $this->assertEquals(1, $result->height());
    }

    public function testSlice(): void
    {
        $result = $this->createDf()->lazy()->slice(1, 2)->collect();
        $this->assertEquals(2, $result->height());
    }

    public function testLimit(): void
    {
        $result = $this->createDf()->lazy()->limit(3)->collect();
        $this->assertEquals(3, $result->height());
    }

    // Aggregations

    public function testCount(): void
    {
        $df = new DataFrame(['a' => [1, 2, 3], 'b' => [4, 5, 6]]);
        $result = $df->lazy()->count()->collect();
        $this->assertEquals(1, $result->height());
    }

    public function testSum(): void
    {
        $df = new DataFrame(['a' => [1, 2, 3]]);
        $result = $df->lazy()->sum()->collect();
        $this->assertEquals(6, $result['a']->item());
    }

    public function testMean(): void
    {
        $df = new DataFrame(['a' => [1, 2, 3]]);
        $result = $df->lazy()->mean()->collect();
        $this->assertEqualsWithDelta(2.0, $result['a']->item(), 0.001);
    }

    public function testMedian(): void
    {
        $df = new DataFrame(['a' => [1, 2, 3]]);
        $result = $df->lazy()->median()->collect();
        $this->assertEqualsWithDelta(2.0, $result['a']->item(), 0.001);
    }

    public function testMin(): void
    {
        $df = new DataFrame(['a' => [1, 2, 3]]);
        $result = $df->lazy()->min()->collect();
        $this->assertEquals(1, $result['a']->item());
    }

    public function testMax(): void
    {
        $df = new DataFrame(['a' => [1, 2, 3]]);
        $result = $df->lazy()->max()->collect();
        $this->assertEquals(3, $result['a']->item());
    }

    public function testStd(): void
    {
        $df = new DataFrame(['a' => [1, 2, 3]]);
        $result = $df->lazy()->std()->collect();
        $this->assertIsFloat($result['a']->item());
    }

    public function testVariance(): void
    {
        $df = new DataFrame(['a' => [1, 2, 3]]);
        $result = $df->lazy()->variance()->collect();
        $this->assertIsFloat($result['a']->item());
    }

    // Column Manipulation

    public function testDrop(): void
    {
        $result = $this->createDf()->lazy()
            ->drop(['salary'])
            ->collect();
        $this->assertEquals(2, $result->width());
        $this->assertEquals(['name', 'age'], $result->getColumns());
    }

    public function testRename(): void
    {
        $result = $this->createDf()->lazy()
            ->rename(['name'], ['full_name'])
            ->collect();
        $this->assertEquals(['full_name', 'age', 'salary'], $result->getColumns());
    }

    public function testUnique(): void
    {
        $df = new DataFrame([
            'a' => [1, 1, 2, 2, 3],
            'b' => ['x', 'x', 'y', 'y', 'z'],
        ]);
        $result = $df->lazy()->unique(['a'])->collect();
        $this->assertEquals(3, $result->height());
    }

    // Null Handling

    public function testDropNulls(): void
    {
        $df = new DataFrame([
            'a' => [1, null, 3],
            'b' => ['x', 'y', null],
        ]);
        $result = $df->lazy()->dropNulls()->collect();
        $this->assertEquals(1, $result->height());
    }

    public function testDropNullsSubset(): void
    {
        $df = new DataFrame([
            'a' => [1, null, 3],
            'b' => ['x', 'y', null],
        ]);
        $result = $df->lazy()->dropNulls(['a'])->collect();
        $this->assertEquals(2, $result->height());
    }

    public function testFillNull(): void
    {
        $df = new DataFrame(['a' => [1, null, 3]]);
        $result = $df->lazy()->fillNull(0)->collect();
        $this->assertEquals(3, $result->height());
    }

    // Join

    public function testJoinInner(): void
    {
        $df1 = new DataFrame(['key' => [1, 2, 3], 'val1' => ['a', 'b', 'c']]);
        $df2 = new DataFrame(['key' => [1, 2, 4], 'val2' => ['x', 'y', 'z']]);

        $result = $df1->lazy()
            ->join($df2->lazy(), [Expr::col('key')], how: 'inner')
            ->collect();
        $this->assertEquals(2, $result->height());
    }

    public function testJoinLeft(): void
    {
        $df1 = new DataFrame(['key' => [1, 2, 3], 'val1' => ['a', 'b', 'c']]);
        $df2 = new DataFrame(['key' => [1, 2, 4], 'val2' => ['x', 'y', 'z']]);

        $result = $df1->lazy()
            ->join($df2->lazy(), [Expr::col('key')], how: 'left')
            ->collect();
        $this->assertEquals(3, $result->height());
    }

    // Miscellaneous

    public function testReverse(): void
    {
        $result = $this->createDf()->lazy()->reverse()->collect();
        $this->assertEquals(4, $result->height());
    }

    public function testExplain(): void
    {
        $plan = $this->createDf()->lazy()
            ->filter(Expr::col('age')->gt(25))
            ->explain();
        $this->assertIsString($plan);
        $this->assertNotEmpty($plan);
    }

    public function testExplainUnoptimized(): void
    {
        $plan = $this->createDf()->lazy()
            ->filter(Expr::col('age')->gt(25))
            ->explain(optimized: false);
        $this->assertIsString($plan);
        $this->assertNotEmpty($plan);
    }

    public function testCache(): void
    {
        $lf = $this->createDf()->lazy()->cache();
        $this->assertInstanceOf(LazyFrame::class, $lf);
        $result = $lf->collect();
        $this->assertEquals(4, $result->height());
    }

    public function testToString(): void
    {
        $lf = $this->createDf()->lazy();
        $str = (string) $lf;
        $this->assertIsString($str);
        $this->assertNotEmpty($str);
    }

    // Method Chaining

    public function testChaining(): void
    {
        $result = $this->createDf()->lazy()
            ->filter(Expr::col('age')->gt(26))
            ->select([Expr::col('name'), Expr::col('salary')])
            ->collect();
        $this->assertEquals(3, $result->height());
        $this->assertEquals(2, $result->width());
    }

    public function testComplexChaining(): void
    {
        $result = $this->createDf()->lazy()
            ->withColumns([
                Expr::col('salary')->mul(12),
            ])
            ->filter(Expr::col('age')->gt(25))
            ->select([Expr::col('name'), Expr::col('salary')])
            ->sort('salary', descending: true)
            ->head(2)
            ->collect();
        $this->assertEquals(2, $result->height());
        $this->assertEquals(2, $result->width());
    }
}
