<?php

namespace Tests\Polars;

use PHPUnit\Framework\TestCase;
use Polars\ClosedInterval;
use Polars\DataFrame;
use Polars\Expr;

class ExprTest extends TestCase
{

    public function testConstruct(): void
    {
        $expr = new Expr('abc');
        $this->assertIsObject($expr);
        $this->assertInstanceOf(Expr::class, $expr);
    }

    public function testConstructWithInt(): void
    {
        $expr = new Expr(42);
        $this->assertInstanceOf(Expr::class, $expr);
    }

    public function testConstructWithFloat(): void
    {
        $expr = new Expr(3.14);
        $this->assertInstanceOf(Expr::class, $expr);
    }

    public function testConstructWithBool(): void
    {
        $expr = new Expr(true);
        $this->assertInstanceOf(Expr::class, $expr);
    }

    public function testConstructWithNull(): void
    {
        $expr = new Expr(null);
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

    // Aggregation methods

    public function testAny(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->any());
    }

    public function testAnyWithIgnoreNulls(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->any(false));
    }

    public function testCount(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->count());
    }

    public function testFirst(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->first());
    }

    public function testLast(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->last());
    }

    public function testLen(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->len());
    }

    public function testMax(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->max());
    }

    public function testMean(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->mean());
    }

    public function testMedian(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->median());
    }

    public function testMin(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->min());
    }

    public function testNUnique(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->nUnique());
    }

    public function testNanMax(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->nanMax());
    }

    public function testNanMin(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->nanMin());
    }

    public function testNullCount(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->nullCount());
    }

    public function testProduct(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->product());
    }

    public function testStd(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->std());
    }

    public function testStdWithDdof(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->std(2));
    }

    public function testSum(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->sum());
    }

    public function testVariance(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->variance());
    }

    public function testVarianceWithDdof(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->variance(2));
    }

    // Comparison methods

    public function testEq(): void
    {
        $expr = Expr::col('abc');
        $expr2 = Expr::col('def');
        $this->assertInstanceOf(Expr::class, $expr->eq($expr2));
    }

    public function testEqWithInt(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->eq(5));
    }

    public function testEqWithFloat(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->eq(3.14));
    }

    public function testEqWithString(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->eq('test'));
    }

    public function testEqWithBool(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->eq(true));
    }

    public function testEqWithNull(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->eq(null));
    }

    public function testEqMissing(): void
    {
        $expr = Expr::col('abc');
        $expr2 = Expr::col('def');
        $this->assertInstanceOf(Expr::class, $expr->eqMissing($expr2));
    }

    public function testEqMissingWithInt(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->eqMissing(5));
    }

    public function testGe(): void
    {
        $expr = Expr::col('abc');
        $expr2 = Expr::col('def');
        $this->assertInstanceOf(Expr::class, $expr->ge($expr2));
    }

    public function testGeWithInt(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->ge(5));
    }

    public function testGt(): void
    {
        $expr = Expr::col('abc');
        $expr2 = Expr::col('def');
        $this->assertInstanceOf(Expr::class, $expr->gt($expr2));
    }

    public function testGtWithInt(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->gt(5));
    }

    public function testLe(): void
    {
        $expr = Expr::col('abc');
        $expr2 = Expr::col('def');
        $this->assertInstanceOf(Expr::class, $expr->le($expr2));
    }

    public function testLeWithInt(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->le(5));
    }

    public function testLt(): void
    {
        $expr = Expr::col('abc');
        $expr2 = Expr::col('def');
        $this->assertInstanceOf(Expr::class, $expr->lt($expr2));
    }

    public function testLtWithInt(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->lt(5));
    }

    public function testNe(): void
    {
        $expr = Expr::col('abc');
        $expr2 = Expr::col('def');
        $this->assertInstanceOf(Expr::class, $expr->ne($expr2));
    }

    public function testNeWithInt(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->ne(5));
    }

    public function testNeqMissing(): void
    {
        $expr = Expr::col('abc');
        $expr2 = Expr::col('def');
        $this->assertInstanceOf(Expr::class, $expr->neqMissing($expr2));
    }

    public function testNeqMissingWithInt(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->neqMissing(5));
    }

    // Arithmetic methods

    public function testAdd(): void
    {
        $expr = Expr::col('abc');
        $expr2 = Expr::col('def');
        $this->assertInstanceOf(Expr::class, $expr->add($expr2));
    }

    public function testAddWithInt(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->add(5));
    }

    public function testAddWithFloat(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->add(3.14));
    }

    public function testFloorDiv(): void
    {
        $expr = Expr::col('abc');
        $expr2 = Expr::col('def');
        $this->assertInstanceOf(Expr::class, $expr->floorDiv($expr2));
    }

    public function testFloorDivWithInt(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->floorDiv(5));
    }

    public function testModulo(): void
    {
        $expr = Expr::col('abc');
        $expr2 = Expr::col('def');
        $this->assertInstanceOf(Expr::class, $expr->modulo($expr2));
    }

    public function testModuloWithInt(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->modulo(5));
    }

    public function testMul(): void
    {
        $expr = Expr::col('abc');
        $expr2 = Expr::col('def');
        $this->assertInstanceOf(Expr::class, $expr->mul($expr2));
    }

    public function testMulWithInt(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->mul(5));
    }

    public function testMulWithFloat(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->mul(3.14));
    }

    public function testNeg(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->neg());
    }

    public function testPow(): void
    {
        $expr = Expr::col('abc');
        $expr2 = Expr::col('def');
        $this->assertInstanceOf(Expr::class, $expr->pow($expr2));
    }

    public function testPowWithInt(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->pow(2));
    }

    public function testPowWithFloat(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->pow(0.5));
    }

    public function testSub(): void
    {
        $expr = Expr::col('abc');
        $expr2 = Expr::col('def');
        $this->assertInstanceOf(Expr::class, $expr->sub($expr2));
    }

    public function testSubWithInt(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->sub(5));
    }

    public function testSubWithFloat(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->sub(3.14));
    }

    public function testDiv(): void
    {
        $expr = Expr::col('abc');
        $expr2 = Expr::col('def');
        $this->assertInstanceOf(Expr::class, $expr->div($expr2));
    }

    public function testDivWithInt(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->div(5));
    }

    public function testDivWithFloat(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->div(3.14));
    }

    public function testXxor(): void
    {
        $expr = Expr::col('abc');
        $expr2 = Expr::col('def');
        $this->assertInstanceOf(Expr::class, $expr->xxor($expr2));
    }

    public function testXxorWithBool(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->xxor(true));
    }

    // Utility methods

    public function testHasNulls(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->hasNulls());
    }

    public function testIsBetweenWithBothClosed(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->isBetween(1, 10, ClosedInterval::Both));
    }

    public function testIsBetweenWithLeftClosed(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->isBetween(1, 10, ClosedInterval::Left));
    }

    public function testIsBetweenWithRightClosed(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->isBetween(1, 10, ClosedInterval::Right));
    }

    public function testIsBetweenWithNoneClosed(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->isBetween(1, 10, ClosedInterval::None));
    }

    public function testIsBetweenWithFloats(): void
    {
        $expr = Expr::col('abc');
        $this->assertInstanceOf(Expr::class, $expr->isBetween(1.5, 10.5, ClosedInterval::Both));
    }

    public function testIsBetweenWithExprBounds(): void
    {
        $expr = Expr::col('abc');
        $lower = Expr::col('lower');
        $upper = Expr::col('upper');
        $this->assertInstanceOf(Expr::class, $expr->isBetween($lower, $upper, ClosedInterval::Both));
    }

    // Integration tests with DataFrame

    public function testExprWithDataFrameSelect(): void
    {
        $aCol = [1, 2, 3, 4, 5];
        $df = new DataFrame([
            'a' => $aCol,
            'b' => [10, 20, 30, 40, 50],
        ]);

        $result = $df->select([Expr::col('a')->sum()]);
        $this->assertInstanceOf(DataFrame::class, $result);
        $this->assertEquals(1, $result->width());
        $this->assertEquals(1, $result->height());
        $this->assertEquals(array_sum($aCol), $result[['a', 0]]->item());
    }

    public function testExprChaining(): void
    {
        $expr = Expr::col('abc')
            ->add(5)
            ->mul(2)
            ->sub(1);
        $this->assertInstanceOf(Expr::class, $expr);
    }

    public function testExprComparisonChaining(): void
    {
        $expr = Expr::col('abc')
            ->gt(0)
            ->xxor(Expr::col('def')->lt(10));
        $this->assertInstanceOf(Expr::class, $expr);
    }
}
