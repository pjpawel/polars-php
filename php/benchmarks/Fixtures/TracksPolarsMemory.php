<?php

namespace PolarsPhpBench\Fixtures;

/**
 * Trait for tracking Polars DataFrame memory usage.
 *
 * PHP's memory_get_peak_usage() does NOT include Rust-side allocations.
 * Use estimatedSize() for the true data footprint of a Polars DataFrame.
 */
trait TracksPolarsMemory
{
    private int $polarsBytes = 0;

    protected function recordPolarsSize(\Polars\DataFrame $df): void
    {
        $this->polarsBytes = $df->estimatedSize();
    }

    public function getPolarsBytes(): int
    {
        return $this->polarsBytes;
    }
}
