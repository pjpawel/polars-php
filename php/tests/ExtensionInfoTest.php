<?php

namespace Tests\Polars;

use PHPUnit\Framework\TestCase;

class ExtensionInfoTest extends TestCase
{
    public function testExtensionIsLoadedAsPolars(): void
    {
        $this->assertContains('polars', array_map('strtolower', get_loaded_extensions()));
    }

    public function testExtensionVersionIsExposed(): void
    {
        $this->assertNotFalse(phpversion('polars'));
    }
}
