<?php

namespace Tests\Polars;

use PHPUnit\Framework\TestCase;
use Polars\DataFrame;
use Polars\Expr;
use Polars\Series;

class DataFrameMethodsTest extends TestCase
{
    private function createDf(): DataFrame
    {
        return new DataFrame([
            'name' => ['Alice', 'Bob', 'Charlie', 'Diana', 'Eve'],
            'age' => [25, 30, 35, 28, 32],
            'score' => [90.5, 85.0, 92.3, 88.1, 91.0],
        ]);
    }

    private function createDfWithNulls(): DataFrame
    {
        return new DataFrame([
            'a' => [1, 2, 3, 4, 5],
            'b' => [10, null, 30, null, 50],
        ]);
    }

    // Phase 1: Core Manipulation

    public function testSort(): void
    {
        $df = $this->createDf();
        $sorted = $df->sort('age');
        $this->assertInstanceOf(DataFrame::class, $sorted);
        $this->assertEquals(5, $sorted->height());
        // First row should have the smallest age
        $row = $sorted->row(0);
        $this->assertEquals(25, $row['age']);
    }

    public function testSortDescending(): void
    {
        $df = $this->createDf();
        $sorted = $df->sort('age', descending: true);
        $row = $sorted->row(0);
        $this->assertEquals(35, $row['age']);
    }

    public function testDrop(): void
    {
        $df = $this->createDf();
        $dropped = $df->drop(['score']);
        $this->assertEquals(2, $dropped->width());
        $this->assertEquals(['name', 'age'], $dropped->columns);
    }

    public function testRename(): void
    {
        $df = $this->createDf();
        $renamed = $df->rename(['name', 'age'], ['fullName', 'years']);
        $this->assertContains('fullName', $renamed->columns);
        $this->assertContains('years', $renamed->columns);
        $this->assertNotContains('name', $renamed->columns);
    }

    public function testFilter(): void
    {
        $df = $this->createDf();
        $filtered = $df->filter(Expr::col('age')->gt(30));
        $this->assertEquals(2, $filtered->height()); // Charlie (35) and Eve (32)
    }

    public function testWithColumns(): void
    {
        $df = $this->createDf();
        $result = $df->withColumns([
            Expr::col('age')->mul(2),
        ]);
        // withColumns modifies 'age' column in place (same name)
        $this->assertEquals(3, $result->width());
        $this->assertContains('age', $result->columns);
    }

    public function testGroupBy(): void
    {
        $df = new DataFrame([
            'group' => ['a', 'a', 'b', 'b'],
            'value' => [1, 2, 3, 4],
        ]);
        $result = $df->groupBy([Expr::col('group')])->sum()->collect();
        $this->assertInstanceOf(DataFrame::class, $result);
        $this->assertEquals(2, $result->height());
    }

    // Phase 2: Aggregations

    public function testSum(): void
    {
        $df = new DataFrame(['x' => [1, 2, 3]]);
        $result = $df->sum();
        $this->assertEquals(6, $result->item());
    }

    public function testMedian(): void
    {
        $df = new DataFrame(['x' => [1, 2, 3, 4, 5]]);
        $result = $df->median();
        $this->assertEquals(3.0, $result->item());
    }

    public function testVariance(): void
    {
        $df = new DataFrame(['x' => [1, 2, 3, 4, 5]]);
        $result = $df->variance();
        $this->assertIsFloat($result->item());
    }

    public function testQuantile(): void
    {
        $df = new DataFrame(['x' => [1, 2, 3, 4, 5]]);
        $result = $df->quantile(0.5);
        $this->assertEquals(3.0, $result->item());
    }

    public function testNullCount(): void
    {
        $df = $this->createDfWithNulls();
        $result = $df->nullCount();
        // Column 'a' has 0 nulls, column 'b' has 2 nulls
        $row = $result->row(0);
        $this->assertEquals(0, $row['a']);
        $this->assertEquals(2, $row['b']);
    }

    public function testProduct(): void
    {
        $df = new DataFrame(['x' => [2, 3, 4]]);
        $result = $df->product();
        $this->assertEquals(24, $result->item());
    }

    // Phase 3: Row/Column Manipulation

    public function testUnique(): void
    {
        $df = new DataFrame([
            'a' => [1, 1, 2, 2, 3],
            'b' => [10, 10, 20, 20, 30],
        ]);
        $result = $df->unique();
        $this->assertEquals(3, $result->height());
    }

    public function testUniqueWithSubset(): void
    {
        $df = new DataFrame([
            'a' => [1, 1, 2, 2, 3],
            'b' => [10, 20, 30, 30, 50],
        ]);
        $result = $df->unique(subset: ['a']);
        $this->assertEquals(3, $result->height());
    }

    public function testDropNulls(): void
    {
        $df = $this->createDfWithNulls();
        $result = $df->dropNulls();
        $this->assertEquals(3, $result->height());
    }

    public function testDropNullsWithSubset(): void
    {
        $df = $this->createDfWithNulls();
        $result = $df->dropNulls(subset: ['a']);
        $this->assertEquals(5, $result->height()); // No nulls in 'a'
    }

    public function testFillNull(): void
    {
        $df = $this->createDfWithNulls();
        $result = $df->fillNull(0);
        $nullCount = $result->nullCount();
        $row = $nullCount->row(0);
        $this->assertEquals(0, $row['b']);
    }

    public function testReverse(): void
    {
        $df = new DataFrame(['x' => [1, 2, 3]]);
        $result = $df->reverse();
        $this->assertEquals(3, $result->row(0)['x']);
        $this->assertEquals(1, $result->row(2)['x']);
    }

    public function testSlice(): void
    {
        $df = $this->createDf();
        $result = $df->slice(1, 2);
        $this->assertEquals(2, $result->height());
    }

    public function testLimit(): void
    {
        $df = $this->createDf();
        $result = $df->limit(3);
        $this->assertEquals(3, $result->height());
    }

    public function testJoin(): void
    {
        $df1 = new DataFrame([
            'id' => [1, 2, 3],
            'name' => ['Alice', 'Bob', 'Charlie'],
        ]);
        $df2 = new DataFrame([
            'id' => [1, 2, 4],
            'score' => [90, 85, 70],
        ]);
        $result = $df1->join($df2, [Expr::col('id')]);
        $this->assertEquals(2, $result->height()); // Only ids 1 and 2 match
    }

    public function testJoinLeft(): void
    {
        $df1 = new DataFrame([
            'id' => [1, 2, 3],
            'name' => ['Alice', 'Bob', 'Charlie'],
        ]);
        $df2 = new DataFrame([
            'id' => [1, 2, 4],
            'score' => [90, 85, 70],
        ]);
        $result = $df1->join($df2, [Expr::col('id')], how: 'left');
        $this->assertEquals(3, $result->height());
    }

    public function testWithRowIndex(): void
    {
        $df = $this->createDf();
        $result = $df->withRowIndex();
        $this->assertContains('index', $result->columns);
        $this->assertEquals(4, $result->width());
    }

    public function testWithRowIndexCustomName(): void
    {
        $df = $this->createDf();
        $result = $df->withRowIndex(name: 'row_num', offset: 1);
        $this->assertContains('row_num', $result->columns);
        $row = $result->row(0);
        $this->assertEquals(1, $row['row_num']);
    }

    // Phase 4: Export/Row Access

    public function testToArray(): void
    {
        $df = new DataFrame([
            'a' => [1, 2],
            'b' => ['x', 'y'],
        ]);
        $arr = $df->toArray();
        $this->assertIsArray($arr);
        $this->assertCount(2, $arr);
        $this->assertEquals(1, $arr[0]['a']);
        $this->assertEquals('y', $arr[1]['b']);
    }

    public function testRow(): void
    {
        $df = $this->createDf();
        $row = $df->row(0);
        $this->assertIsArray($row);
        $this->assertEquals('Alice', $row['name']);
        $this->assertEquals(25, $row['age']);
    }

    public function testRowNegativeIndex(): void
    {
        $df = $this->createDf();
        $row = $df->row(-1);
        $this->assertEquals('Eve', $row['name']);
    }

    public function testRows(): void
    {
        $df = $this->createDf();
        $rows = $df->rows();
        $this->assertCount(5, $rows);
    }

    // Phase 5: Direct DataFrame Methods

    public function testVstack(): void
    {
        $df1 = new DataFrame(['a' => [1, 2], 'b' => [3, 4]]);
        $df2 = new DataFrame(['a' => [5, 6], 'b' => [7, 8]]);
        $result = $df1->vstack($df2);
        $this->assertEquals(4, $result->height());
    }

    public function testHstack(): void
    {
        $df = new DataFrame(['a' => [1, 2, 3]]);
        $s = new Series('b', [4, 5, 6]);
        $result = $df->hstack([$s]);
        $this->assertEquals(2, $result->width());
        $this->assertContains('b', $result->columns);
    }

    public function testEquals(): void
    {
        $df1 = new DataFrame(['a' => [1, 2, 3]]);
        $df2 = new DataFrame(['a' => [1, 2, 3]]);
        $df3 = new DataFrame(['a' => [1, 2, 4]]);
        $this->assertTrue($df1->equals($df2));
        $this->assertFalse($df1->equals($df3));
    }

    public function testEstimatedSize(): void
    {
        $df = $this->createDf();
        $size = $df->estimatedSize();
        $this->assertIsInt($size);
        $this->assertGreaterThan(0, $size);
    }

    public function testGetColumnIndex(): void
    {
        $df = $this->createDf();
        $this->assertEquals(0, $df->getColumnIndex('name'));
        $this->assertEquals(1, $df->getColumnIndex('age'));
        $this->assertEquals(-1, $df->getColumnIndex('nonexistent'));
    }

    public function testClear(): void
    {
        $df = $this->createDf();
        $cleared = $df->clear();
        $this->assertEquals(0, $cleared->height());
        $this->assertEquals(3, $cleared->width());
    }

    public function testRechunk(): void
    {
        $df = $this->createDf();
        $rechunked = $df->rechunk();
        $this->assertEquals($df->height(), $rechunked->height());
    }

    public function testShrinkToFit(): void
    {
        $df = $this->createDf();
        $df->shrinkToFit(); // Should not throw
        $this->assertEquals(5, $df->height());
    }

    public function testIsDuplicated(): void
    {
        $df = new DataFrame([
            'a' => [1, 1, 2, 3, 3],
            'b' => [10, 10, 20, 30, 30],
        ]);
        $mask = $df->isDuplicated();
        $this->assertInstanceOf(Series::class, $mask);
        $this->assertEquals(5, $mask->len());
    }

    public function testIsUnique(): void
    {
        $df = new DataFrame([
            'a' => [1, 1, 2, 3, 3],
            'b' => [10, 10, 20, 30, 30],
        ]);
        $mask = $df->isUnique();
        $this->assertInstanceOf(Series::class, $mask);
        $this->assertEquals(5, $mask->len());
    }

    // Phase 6: Advanced Lazy Delegates

    public function testShift(): void
    {
        $df = new DataFrame(['x' => [1, 2, 3, 4, 5]]);
        $result = $df->shift(1);
        $this->assertEquals(5, $result->height());
        // First value should be null after shift
        $row = $result->row(0);
        $this->assertNull($row['x']);
    }

    public function testGatherEvery(): void
    {
        $df = new DataFrame(['x' => [1, 2, 3, 4, 5, 6]]);
        $result = $df->gatherEvery(2);
        $this->assertEquals(3, $result->height());
    }

    public function testCast(): void
    {
        $df = new DataFrame(['x' => [1, 2, 3]]);
        $result = $df->cast(['x' => 'float64']);
        $this->assertInstanceOf(DataFrame::class, $result);
        $row = $result->row(0);
        $this->assertIsFloat($row['x']);
    }

    public function testUnpivot(): void
    {
        $df = new DataFrame([
            'id' => [1, 2],
            'a' => [10, 20],
            'b' => [30, 40],
        ]);
        $result = $df->unpivot(on: ['a', 'b'], index: ['id']);
        $this->assertEquals(4, $result->height());
        $this->assertContains('variable', $result->columns);
        $this->assertContains('value', $result->columns);
    }

    // Phase 7: Descriptive Methods

    public function testSchema(): void
    {
        $df = $this->createDf();
        $schema = $df->schema;
        $this->assertIsString($schema);
        $this->assertStringContainsString('name', $schema);
    }

    public function testNUnique(): void
    {
        $df = new DataFrame([
            'a' => [1, 1, 2, 3, 3],
            'b' => ['x', 'y', 'z', 'x', 'y'],
        ]);
        $result = $df->nUnique();
        $this->assertEquals(1, $result->height());
    }

    public function testGlimpse(): void
    {
        $df = $this->createDf();
        $glimpse = $df->glimpse();
        $this->assertIsString($glimpse);
        $this->assertStringContainsString('Rows: 5', $glimpse);
        $this->assertStringContainsString('Columns: 3', $glimpse);
    }

    public function testDescribe(): void
    {
        $df = new DataFrame(['x' => [1, 2, 3, 4, 5]]);
        $result = $df->describe();
        $this->assertInstanceOf(DataFrame::class, $result);
        $this->assertGreaterThan(1, $result->height());
    }

    // Phase 8: Remaining Methods

    public function testSample(): void
    {
        $df = $this->createDf();
        $result = $df->sample(3, seed: 42);
        $this->assertEquals(3, $result->height());
    }

    public function testTranspose(): void
    {
        $df = new DataFrame([
            'a' => [1, 2],
            'b' => [3, 4],
        ]);
        $result = $df->transpose();
        $this->assertEquals(2, $result->height());
        $this->assertEquals(2, $result->width());
    }

    public function testTransposeWithHeader(): void
    {
        $df = new DataFrame([
            'a' => [1, 2],
            'b' => [3, 4],
        ]);
        $result = $df->transpose(includeHeader: true);
        $this->assertContains('column', $result->columns);
    }

    public function testTopK(): void
    {
        $df = $this->createDf();
        $result = $df->topK(2, 'age');
        $this->assertEquals(2, $result->height());
        $row = $result->row(0);
        $this->assertEquals(35, $row['age']); // Highest age first
    }

    public function testBottomK(): void
    {
        $df = $this->createDf();
        $result = $df->bottomK(2, 'age');
        $this->assertEquals(2, $result->height());
        $row = $result->row(0);
        $this->assertEquals(25, $row['age']); // Lowest age first
    }
}
