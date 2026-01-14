<?php

namespace Tests\Polars;

use Exception;
use PHPUnit\Framework\TestCase;
use Polars\DataFrame;
use Polars\Expr;

class SimpleDataFrameTest extends TestCase
{

    const string COL_1 = 'col1';
    const string COL_2 = 'series56';
    const string COL_3 = 'rare';


    private function prepareArray(): array
    {
        return [
            self::COL_1 => ['a', 'b', 'c', 'd', 'e', 'f'],
            self::COL_2 => [1, 2, 3, 4, 5, 6],
            self::COL_3 => ['a', null, 'e', 'z', 'x', 'y']
        ];
    }

    public function testConstruct(): void
    {
        $df = new DataFrame($this->prepareArray());
        $this->assertIsObject($df);
        $this->assertInstanceOf(DataFrame::class, $df);
    }

    public function testGetColumns(): void
    {
        $df = new DataFrame($this->prepareArray());
        $this->assertIsArray($df->getColumns());
        $this->assertCount(3, $df->getColumns());
    }

    public function testSetColumns(): void
    {
        $df = new DataFrame($this->prepareArray());
        $this->assertCount(3, $df->getColumns());
        $df->setColumns(['a', 'b', 'c']);
        $this->assertCount(3, $df->getColumns());
        $this->assertEquals(['a', 'b', 'c'], $df->getColumns());
    }

    public function testSetColumnsException(): void
    {
        $df = new DataFrame($this->prepareArray());
        $this->assertCount(3, $df->getColumns());
        $this->expectException(Exception::class);
        $df->setColumns(['a', 'b', 'c', 'd']);
    }

    public function testHeight(): void
    {
        $df = new DataFrame($this->prepareArray());
        $this->assertIsInt($df->height());
        $this->assertEquals(6, $df->height());
    }

    public function testWidth(): void
    {
        $df = new DataFrame($this->prepareArray());
        $this->assertIsInt($df->width());
        $this->assertEquals(3, $df->width());
    }

    public function testShape(): void
    {
        $df = new DataFrame($this->prepareArray());
        $this->assertIsArray($df->shape());
        $this->assertEquals([6, 3], $df->shape());
    }

    public function testCopy(): void
    {
        $df = new DataFrame($this->prepareArray());
        $copy = $df->copy();
        $this->assertEquals($df, $copy);
    }

    public function testIsEmpty(): void
    {
        $df = new DataFrame($this->prepareArray());
        $this->assertEquals(false, $df->isEmpty());
    }

    public function testSelect(): void
    {
        $df = new DataFrame($this->prepareArray());
        $expr = Expr::col(self::COL_2);
        $expr = $expr->ge(3);
        $nDf = $df->select([$expr]);
        $this->assertInstanceOf(DataFrame::class, $nDf);
    }

}
