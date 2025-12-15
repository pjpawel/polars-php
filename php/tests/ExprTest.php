<?php

namespace Tests\Polars;

use PHPUnit\Framework\TestCase;
use Polars\Expr;

class ExprTest extends TestCase
{

    public function testConstruct(): void
    {
        $expr = new Expr('abc');
        $this->assertIsObject($expr);
        $this->assertInstanceOf(Expr::class, $expr);
    }

    public function testCol(): void
    {
        $expr = Expr::col('abc');
        $this->assertIsObject($expr);
        $this->assertInstanceOf(Expr::class, $expr);
    }

    public function testCols(): void
    {
        $expr = Expr::cols(['abc', 'def', 'ghi']);
        $this->assertIsObject($expr);
        $this->assertInstanceOf(Expr::class, $expr);
    }

    public function testAll(): void
    {
        $expr = Expr::all();
        $this->assertIsObject($expr);
        $this->assertInstanceOf(Expr::class, $expr);
    }

    public function testEq(): void
    {
        $expr = Expr::col('abc');
        $expr2 = Expr::col('def');
        $this->assertInstanceOf(Expr::class, $expr->eq($expr2));
    }
}