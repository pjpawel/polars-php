<?php

namespace Tests\Polars;

use PHPUnit\Framework\TestCase;
use Polars\DataFrame;
use Polars\LazyFrame;
use Polars\QuantileMethod;

class LazyFrameAggregationTest extends TestCase
{
    private function makeDf(): DataFrame
    {
        return new DataFrame([
            'a' => [1, 2, 3, 4],
            'b' => [4, 5, 6, 7],
        ]);
    }

    public function testSum(): void
    {
        $result = $this->makeDf()->lazy()->sum()->collect();
        $this->assertEquals(1, $result->height());
        $this->assertEquals(['a', 'b'], $result->columns);
    }

    public function testMean(): void
    {
        $result = $this->makeDf()->lazy()->mean()->collect();
        $this->assertEquals(1, $result->height());
    }

    public function testMax(): void
    {
        $result = $this->makeDf()->lazy()->max()->collect();
        $this->assertEquals(1, $result->height());
    }

    public function testMin(): void
    {
        $result = $this->makeDf()->lazy()->min()->collect();
        $this->assertEquals(1, $result->height());
    }

    public function testMedian(): void
    {
        $result = $this->makeDf()->lazy()->median()->collect();
        $this->assertEquals(1, $result->height());
    }

    public function testNullCount(): void
    {
        $df = new DataFrame([
            'a' => [1, null, 3],
            'b' => [null, 5, 6],
        ]);
        $result = $df->lazy()->nullCount()->collect();
        $this->assertEquals(1, $result->height());
    }

    public function testStdDefault(): void
    {
        $result = $this->makeDf()->lazy()->std()->collect();
        $this->assertEquals(1, $result->height());
    }

    public function testVarianceDefault(): void
    {
        $result = $this->makeDf()->lazy()->variance()->collect();
        $this->assertEquals(1, $result->height());
    }

    public function testQuantileLinear(): void
    {
        $result = $this->makeDf()->lazy()->quantile(0.5, QuantileMethod::Linear)->collect();
        $this->assertEquals(1, $result->height());
    }

    public function testQuantileBelowZeroThrows(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('quantile must be between 0 and 1');
        $this->makeDf()->lazy()->quantile(-0.1, QuantileMethod::Linear)->collect();
    }

    public function testQuantileAboveOneThrows(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('quantile must be between 0 and 1');
        $this->makeDf()->lazy()->quantile(1.5, QuantileMethod::Linear)->collect();
    }
}
