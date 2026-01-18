<?php

namespace Tests\Polars;

use Exception;
use PHPUnit\Framework\TestCase;
use Polars\DataFrame;
use Polars\Series;

class SeriesTest extends TestCase
{
    // ==================== Constructor Tests ====================

    public function testConstructWithIntegers(): void
    {
        $s = new Series('numbers', [1, 2, 3, 4, 5]);
        $this->assertInstanceOf(Series::class, $s);
        $this->assertEquals('numbers', $s->getName());
        $this->assertEquals(5, $s->len());
    }

    public function testConstructWithFloats(): void
    {
        $s = new Series('decimals', [1.5, 2.5, 3.5]);
        $this->assertInstanceOf(Series::class, $s);
        $this->assertEquals(3, $s->len());
    }

    public function testConstructWithStrings(): void
    {
        $s = new Series('names', ['Alice', 'Bob', 'Charlie']);
        $this->assertInstanceOf(Series::class, $s);
        $this->assertEquals(3, $s->len());
    }

    public function testConstructWithBooleans(): void
    {
        $s = new Series('flags', [true, false, true]);
        $this->assertInstanceOf(Series::class, $s);
        $this->assertEquals(3, $s->len());
    }

    public function testConstructEmpty(): void
    {
        $s = new Series('empty', []);
        $this->assertInstanceOf(Series::class, $s);
        $this->assertEquals(0, $s->len());
        $this->assertTrue($s->isEmpty());
    }

    // ==================== Attribute Tests ====================

    public function testName(): void
    {
        $s = new Series('test_name', [1, 2, 3]);
        $this->assertEquals('test_name', $s->getName());
    }

    public function testShape(): void
    {
        $s = new Series('x', [1, 2, 3, 4, 5]);
        $this->assertEquals([5], $s->getShape());
    }

    public function testLen(): void
    {
        $s = new Series('x', [1, 2, 3]);
        $this->assertEquals(3, $s->len());
    }

    public function testIsEmpty(): void
    {
        $empty = new Series('empty', []);
        $notEmpty = new Series('x', [1]);

        $this->assertTrue($empty->isEmpty());
        $this->assertFalse($notEmpty->isEmpty());
    }

    public function testCountable(): void
    {
        $s = new Series('x', [1, 2, 3, 4, 5]);
        $this->assertCount(5, $s);
    }

    // ==================== Array Access Tests ====================

    public function testOffsetGet(): void
    {
        $s = new Series('x', [10, 20, 30, 40, 50]);
        $this->assertEquals(10, $s[0]);
        $this->assertEquals(30, $s[2]);
        $this->assertEquals(50, $s[4]);
    }

    public function testOffsetGetNegative(): void
    {
        $s = new Series('x', [10, 20, 30, 40, 50]);
        $this->assertEquals(50, $s[-1]);
        $this->assertEquals(40, $s[-2]);
        $this->assertEquals(10, $s[-5]);
    }

    public function testOffsetExists(): void
    {
        $s = new Series('x', [1, 2, 3]);
        $this->assertTrue(isset($s[0]));
        $this->assertTrue(isset($s[2]));
        $this->assertFalse(isset($s[5]));
    }

    public function testOffsetGetOutOfBounds(): void
    {
        $s = new Series('x', [1, 2, 3]);
        $this->expectException(Exception::class);
        $val = $s[10];
    }

    // ==================== Element Access Tests ====================

    public function testHead(): void
    {
        $s = new Series('x', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $head = $s->head(3);
        $this->assertEquals(3, $head->len());
        $this->assertEquals(1, $head[0]);
        $this->assertEquals(3, $head[2]);
    }

    public function testTail(): void
    {
        $s = new Series('x', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $tail = $s->tail(3);
        $this->assertEquals(3, $tail->len());
        $this->assertEquals(8, $tail[0]);
        $this->assertEquals(10, $tail[2]);
    }

    public function testItem(): void
    {
        $s = new Series('x', [42]);
        $this->assertEquals(42, $s->item());
    }

    public function testItemException(): void
    {
        $s = new Series('x', [1, 2, 3]);
        $this->expectException(Exception::class);
        $s->item();
    }

    public function testFirst(): void
    {
        $s = new Series('x', [10, 20, 30]);
        $this->assertEquals(10, $s->first());
    }

    public function testLast(): void
    {
        $s = new Series('x', [10, 20, 30]);
        $this->assertEquals(30, $s->last());
    }

    public function testSlice(): void
    {
        $s = new Series('x', [1, 2, 3, 4, 5]);
        $sliced = $s->slice(1, 3);
        $this->assertEquals(3, $sliced->len());
        $this->assertEquals(2, $sliced[0]);
        $this->assertEquals(4, $sliced[2]);
    }

    // ==================== Aggregation Tests ====================

    public function testSum(): void
    {
        $s = new Series('x', [1, 2, 3, 4, 5]);
        $this->assertEquals(15, $s->sum());
    }

    public function testMean(): void
    {
        $s = new Series('x', [1, 2, 3, 4, 5]);
        $this->assertEquals(3.0, $s->mean());
    }

    public function testMedian(): void
    {
        $s = new Series('x', [1, 2, 3, 4, 5]);
        $this->assertEquals(3.0, $s->median());
    }

    public function testMin(): void
    {
        $s = new Series('x', [5, 2, 8, 1, 9]);
        $this->assertEquals(1, $s->min());
    }

    public function testMax(): void
    {
        $s = new Series('x', [5, 2, 8, 1, 9]);
        $this->assertEquals(9, $s->max());
    }

    public function testStd(): void
    {
        $s = new Series('x', [2, 4, 4, 4, 5, 5, 7, 9]);
        $std = $s->std(0); // population std
        $this->assertEqualsWithDelta(2.0, $std, 0.001);
    }

    public function testVariance(): void
    {
        $s = new Series('x', [2, 4, 4, 4, 5, 5, 7, 9]);
        $var = $s->variance(0); // population variance
        $this->assertEqualsWithDelta(4.0, $var, 0.001);
    }

    public function testProduct(): void
    {
        $s = new Series('x', [1, 2, 3, 4]);
        $this->assertEquals(24, $s->product());
    }

    public function testCountNonNull(): void
    {
        $s = new Series('x', [1, 2, null, 4, null]);
        $this->assertEquals(3, $s->countNonNull());
    }

    public function testNullCount(): void
    {
        $s = new Series('x', [1, 2, null, 4, null]);
        $this->assertEquals(2, $s->nullCount());
    }

    public function testNUnique(): void
    {
        $s = new Series('x', [1, 2, 2, 3, 3, 3]);
        $this->assertEquals(3, $s->nUnique());
    }

    // ==================== Boolean Operations Tests ====================

    public function testIsNull(): void
    {
        $s = new Series('x', [1, null, 3]);
        $result = $s->isNull();
        $this->assertInstanceOf(Series::class, $result);
        $this->assertFalse($result[0]);
        $this->assertTrue($result[1]);
        $this->assertFalse($result[2]);
    }

    public function testIsNotNull(): void
    {
        $s = new Series('x', [1, null, 3]);
        $result = $s->isNotNull();
        $this->assertTrue($result[0]);
        $this->assertFalse($result[1]);
        $this->assertTrue($result[2]);
    }

    public function testAny(): void
    {
        $allFalse = new Series('x', [false, false, false]);
        $someTrueArr = new Series('x', [false, true, false]);

        $this->assertFalse($allFalse->any());
        $this->assertTrue($someTrueArr->any());
    }

    public function testAll(): void
    {
        $allTrue = new Series('x', [true, true, true]);
        $someFalse = new Series('x', [true, false, true]);

        $this->assertTrue($allTrue->all());
        $this->assertFalse($someFalse->all());
    }

    // ==================== Comparison Tests ====================

    public function testEq(): void
    {
        $s = new Series('x', [1, 2, 3, 2, 1]);
        $result = $s->eq(2);
        $this->assertFalse($result[0]);
        $this->assertTrue($result[1]);
        $this->assertFalse($result[2]);
        $this->assertTrue($result[3]);
    }

    public function testNe(): void
    {
        $s = new Series('x', [1, 2, 3]);
        $result = $s->ne(2);
        $this->assertTrue($result[0]);
        $this->assertFalse($result[1]);
        $this->assertTrue($result[2]);
    }

    public function testGt(): void
    {
        $s = new Series('x', [1, 2, 3, 4, 5]);
        $result = $s->gt(3);
        $this->assertFalse($result[0]);
        $this->assertFalse($result[2]);
        $this->assertTrue($result[3]);
        $this->assertTrue($result[4]);
    }

    public function testGe(): void
    {
        $s = new Series('x', [1, 2, 3, 4, 5]);
        $result = $s->ge(3);
        $this->assertFalse($result[0]);
        $this->assertTrue($result[2]);
        $this->assertTrue($result[3]);
    }

    public function testLt(): void
    {
        $s = new Series('x', [1, 2, 3, 4, 5]);
        $result = $s->lt(3);
        $this->assertTrue($result[0]);
        $this->assertTrue($result[1]);
        $this->assertFalse($result[2]);
    }

    public function testLe(): void
    {
        $s = new Series('x', [1, 2, 3, 4, 5]);
        $result = $s->le(3);
        $this->assertTrue($result[0]);
        $this->assertTrue($result[2]);
        $this->assertFalse($result[3]);
    }

    // ==================== Data Manipulation Tests ====================

    public function testSort(): void
    {
        $s = new Series('x', [3, 1, 4, 1, 5, 9, 2, 6]);
        $sorted = $s->sort();
        $this->assertEquals(1, $sorted[0]);
        $this->assertEquals(1, $sorted[1]);
        $this->assertEquals(2, $sorted[2]);
        $this->assertEquals(9, $sorted[-1]);
    }

    public function testSortDescending(): void
    {
        $s = new Series('x', [3, 1, 4, 1, 5]);
        $sorted = $s->sort(descending: true);
        $this->assertEquals(5, $sorted[0]);
        $this->assertEquals(1, $sorted[-1]);
    }

    public function testReverse(): void
    {
        $s = new Series('x', [1, 2, 3, 4, 5]);
        $reversed = $s->reverse();
        $this->assertEquals(5, $reversed[0]);
        $this->assertEquals(4, $reversed[1]);
        $this->assertEquals(1, $reversed[4]);
    }

    public function testUnique(): void
    {
        $s = new Series('x', [1, 2, 2, 3, 3, 3]);
        $unique = $s->unique();
        $this->assertEquals(3, $unique->len());
    }

    public function testDropNulls(): void
    {
        $s = new Series('x', [1, null, 2, null, 3]);
        $dropped = $s->dropNulls();
        $this->assertEquals(3, $dropped->len());
        $this->assertEquals(1, $dropped[0]);
        $this->assertEquals(2, $dropped[1]);
        $this->assertEquals(3, $dropped[2]);
    }

    // ==================== Utility Tests ====================

    public function testToArray(): void
    {
        $s = new Series('x', [1, 2, 3]);
        $arr = $s->toArray();
        $this->assertIsArray($arr);
        $this->assertEquals([1, 2, 3], $arr);
    }

    public function testRename(): void
    {
        $s = new Series('old', [1, 2, 3]);
        $renamed = $s->rename('new');
        $this->assertEquals('new', $renamed->getName());
        $this->assertEquals('old', $s->getName()); // original unchanged
    }

    public function testAlias(): void
    {
        $s = new Series('original', [1, 2, 3]);
        $aliased = $s->alias('aliased');
        $this->assertEquals('aliased', $aliased->getName());
    }

    public function testCopy(): void
    {
        $s = new Series('x', [1, 2, 3]);
        $copy = $s->copy();
        $this->assertInstanceOf(Series::class, $copy);
        $this->assertEquals($s->len(), $copy->len());
        $this->assertEquals($s[0], $copy[0]);
    }

    public function testCast(): void
    {
        $s = new Series('x', [1, 2, 3]);
        $floats = $s->cast('float64');
        $this->assertInstanceOf(Series::class, $floats);
        $this->assertIsFloat($floats[0]);
    }

    public function testToString(): void
    {
        $s = new Series('x', [1, 2, 3]);
        $str = (string)$s;
        $this->assertIsString($str);
        $this->assertStringContainsString('x', $str);
    }

    // ==================== DataFrame Integration Tests ====================

    public function testDataFrameColumn(): void
    {
        $df = new DataFrame([
            'a' => [1, 2, 3],
            'b' => [4, 5, 6],
        ]);

        $series = $df->column('a');
        $this->assertInstanceOf(Series::class, $series);
        $this->assertEquals('a', $series->getName());
        $this->assertEquals(3, $series->len());
        $this->assertEquals(1, $series[0]);
        $this->assertEquals(6, $series->sum());
    }

    public function testDataFrameGetSeries(): void
    {
        $df = new DataFrame([
            'x' => [1, 2],
            'y' => [3, 4],
        ]);

        $seriesArr = $df->getSeries();
        $this->assertIsArray($seriesArr);
        $this->assertCount(2, $seriesArr);
        $this->assertInstanceOf(Series::class, $seriesArr[0]);
        $this->assertInstanceOf(Series::class, $seriesArr[1]);
    }
}
