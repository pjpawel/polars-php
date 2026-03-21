<?php

namespace Tests\Polars;

use PHPUnit\Framework\TestCase;
use Polars\Series;

class SeriesFlagsTest extends TestCase
{
    public function testUnsortedSeriesFlags(): void
    {
        $s = new Series('a', [3, 1, 2]);
        $flags = $s->getFlags();

        $this->assertArrayHasKey('SORTED_ASC', $flags);
        $this->assertArrayHasKey('SORTED_DESC', $flags);
        $this->assertFalse($flags['SORTED_ASC']);
        $this->assertFalse($flags['SORTED_DESC']);
    }

    public function testSortedAscSeriesFlags(): void
    {
        $s = new Series('a', [3, 1, 2]);
        $sorted = $s->sort();
        $flags = $sorted->getFlags();

        $this->assertTrue($flags['SORTED_ASC']);
        $this->assertFalse($flags['SORTED_DESC']);
    }

    public function testSortedDescSeriesFlags(): void
    {
        $s = new Series('a', [3, 1, 2]);
        $sorted = $s->sort(descending: true);
        $flags = $sorted->getFlags();

        $this->assertFalse($flags['SORTED_ASC']);
        $this->assertTrue($flags['SORTED_DESC']);
    }

    public function testNoFastExplodeFlagForNonListSeries(): void
    {
        $s = new Series('a', [1, 2, 3]);
        $flags = $s->getFlags();

        $this->assertArrayNotHasKey('FAST_EXPLODE', $flags);
    }

    public function testFastExplodeFlagForListSeries(): void
    {
        $s = new Series('a', [1, 2, 3]);
        $list = $s->implode();
        $flags = $list->getFlags();

        $this->assertArrayHasKey('FAST_EXPLODE', $flags);
        $this->assertIsBool($flags['FAST_EXPLODE']);
    }
}
