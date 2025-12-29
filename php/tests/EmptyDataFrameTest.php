<?php

namespace Tests\Polars;

use Exception;
use PHPUnit\Framework\TestCase;
use Polars\DataFrame;

class EmptyDataFrameTest extends TestCase
{

    public function testConstruct(): void
    {
        $df = new DataFrame([], true);
        $this->assertIsObject($df);
        $this->assertInstanceOf(DataFrame::class, $df);
    }

    public function testGetColumns(): void
    {
        $df = new DataFrame([], true);
        $this->assertIsArray($df->getColumns());
        $this->assertCount(0, $df->getColumns());
    }

    public function testSetColumns(): void
    {
        $df = new DataFrame([], true);
        $this->assertCount(0, $df->getColumns());
        $this->expectException(Exception::class);
        $df->setColumns(['a', 'b', 'c']);
    }

    public function testHeight(): void
    {
        $df = new DataFrame([], true);
        $this->assertIsInt($df->height());
        $this->assertEquals(0, $df->height());
    }

    public function testWidth(): void
    {
        $df = new DataFrame([], true);
        $this->assertIsInt($df->width());
        $this->assertEquals(0, $df->width());
    }

    public function testShape(): void
    {
        $df = new DataFrame([], true);
        $this->assertIsArray($df->shape());
        $this->assertEquals([0, 0], $df->shape());
    }

    public function testCopy(): void
    {
        $df = new DataFrame([], true);
        $copy = $df->copy();
        $this->assertEquals($df, $copy);
    }

    public function testIsEmpty(): void
    {
        $df = new DataFrame([], true);
        $this->assertEquals(true, $df->isEmpty());
    }

}
