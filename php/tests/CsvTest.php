<?php

namespace Tests\Polars;

use Exception;
use PHPUnit\Framework\TestCase;
use Polars\DataFrame;

class CsvTest extends TestCase
{
    private const string FIXTURES_DIR = __DIR__ . '/fixtures';
    private const string OUTPUT_DIR = __DIR__ . '/output';

    public static function setUpBeforeClass(): void
    {
        if (!is_dir(self::OUTPUT_DIR)) {
            mkdir(self::OUTPUT_DIR, 0755, true);
        }
    }

    public static function tearDownAfterClass(): void
    {
        // Clean up output files
        $files = glob(self::OUTPUT_DIR . '/*.csv');
        foreach ($files as $file) {
            unlink($file);
        }
        if (is_dir(self::OUTPUT_DIR)) {
            rmdir(self::OUTPUT_DIR);
        }
    }

    // Basic CSV Loading

    public function testFromCsvSimple(): void
    {
        $df = DataFrame::fromCsv(self::FIXTURES_DIR . '/simple.csv');

        $this->assertInstanceOf(DataFrame::class, $df);
        $this->assertEquals(5, $df->height());
        $this->assertEquals(4, $df->width());
        $this->assertEquals(['name', 'age', 'city', 'salary'], $df->getColumns());
    }

    public function testFromCsvColumnValues(): void
    {
        $df = DataFrame::fromCsv(self::FIXTURES_DIR . '/simple.csv');

        // Check first row values
        $firstRow = $df[0];
        $this->assertEquals('Alice', $firstRow['name']->item());
        $this->assertEquals(25, $firstRow['age']->item());
        $this->assertEquals('NYC', $firstRow['city']->item());
        $this->assertEquals(50000, $firstRow['salary']->item());
    }

    public function testFromCsvLastRow(): void
    {
        $df = DataFrame::fromCsv(self::FIXTURES_DIR . '/simple.csv');

        // Check last row using negative index
        $lastRow = $df[-1];
        $this->assertEquals('Eve', $lastRow['name']->item());
        $this->assertEquals(32, $lastRow['age']->item());
        $this->assertEquals('Seattle', $lastRow['city']->item());
        $this->assertEquals(70000, $lastRow['salary']->item());
    }

    public function testFromCsvNoHeader(): void
    {
        $df = DataFrame::fromCsv(self::FIXTURES_DIR . '/no_header.csv', headerIncluded: false);

        $this->assertInstanceOf(DataFrame::class, $df);
        $this->assertEquals(3, $df->height());
        $this->assertEquals(3, $df->width());
        // Without header, columns get default names
        $this->assertNotEquals(['name', 'age', 'city'], $df->getColumns());
    }

    public function testFromCsvCustomSeparator(): void
    {
        $df = DataFrame::fromCsv(self::FIXTURES_DIR . '/semicolon.csv', separator: ';');

        $this->assertInstanceOf(DataFrame::class, $df);
        $this->assertEquals(3, $df->height());
        $this->assertEquals(['product', 'price', 'quantity'], $df->getColumns());

        $this->assertEquals('Apple', $df[0]['product']->item());
        $this->assertEquals(1.50, $df[0]['price']->item());
        $this->assertEquals(100, $df[0]['quantity']->item());
    }

    public function testFromCsvNumeric(): void
    {
        $df = DataFrame::fromCsv(self::FIXTURES_DIR . '/numeric.csv');

        $this->assertEquals(5, $df->height());
        $this->assertEquals(['a', 'b', 'c'], $df->getColumns());

        // Verify numeric values
        $this->assertEquals(1, $df[0]['a']->item());
        $this->assertEquals(50, $df[4]['b']->item());
        $this->assertEquals(300, $df[2]['c']->item());
    }

    public function testFromCsvNonExistentFile(): void
    {
        $this->expectException(Exception::class);
        DataFrame::fromCsv(self::FIXTURES_DIR . '/nonexistent.csv');
    }

    public function testFromCsvInvalidSeparator(): void
    {
        $this->expectException(Exception::class);
        DataFrame::fromCsv(self::FIXTURES_DIR . '/simple.csv', separator: 'too_long');
    }

    // CSV Writing

    public function testWriteCsvBasic(): void
    {
        $df = new DataFrame([
            'x' => [1, 2, 3],
            'y' => [4, 5, 6],
        ]);

        $outputPath = self::OUTPUT_DIR . '/basic_output.csv';
        $df->writeCsv($outputPath, includeHeader: true);

        $this->assertFileExists($outputPath);

        // Read back and verify
        $dfRead = DataFrame::fromCsv($outputPath);
        $this->assertEquals(3, $dfRead->height());
        $this->assertEquals(['x', 'y'], $dfRead->getColumns());
        $this->assertEquals(1, $dfRead[0]['x']->item());
        $this->assertEquals(6, $dfRead[2]['y']->item());
    }

    public function testWriteCsvNoHeader(): void
    {
        $df = new DataFrame([
            'a' => [10, 20],
            'b' => [30, 40],
        ]);

        $outputPath = self::OUTPUT_DIR . '/no_header_output.csv';
        $df->writeCsv($outputPath, includeHeader: false);

        $this->assertFileExists($outputPath);

        // Read back without header
        $dfRead = DataFrame::fromCsv($outputPath, headerIncluded: false);
        $this->assertEquals(2, $dfRead->height());
    }

    public function testWriteCsvCustomSeparator(): void
    {
        $df = new DataFrame([
            'col1' => ['a', 'b', 'c'],
            'col2' => [1, 2, 3],
        ]);

        $outputPath = self::OUTPUT_DIR . '/semicolon_output.csv';
        $df->writeCsv($outputPath, includeHeader: true, separator: ';');

        $this->assertFileExists($outputPath);

        // Read back with same separator
        $dfRead = DataFrame::fromCsv($outputPath, separator: ';');
        $this->assertEquals(3, $dfRead->height());
        $this->assertEquals(['col1', 'col2'], $dfRead->getColumns());
    }

    public function testWriteCsvRoundTrip(): void
    {
        $original = new DataFrame([
            'name' => ['Test1', 'Test2', 'Test3'],
            'value' => [100, 200, 300],
            'ratio' => [1.5, 2.5, 3.5],
        ]);

        $outputPath = self::OUTPUT_DIR . '/roundtrip.csv';
        $original->writeCsv($outputPath, includeHeader: true);

        $loaded = DataFrame::fromCsv($outputPath);

        $this->assertEquals($original->height(), $loaded->height());
        $this->assertEquals($original->width(), $loaded->width());
        $this->assertEquals($original->getColumns(), $loaded->getColumns());

        // Verify values
        for ($i = 0; $i < $original->height(); $i++) {
            $this->assertEquals(
                $original[$i]['name']->item(),
                $loaded[$i]['name']->item()
            );
            $this->assertEquals(
                $original[$i]['value']->item(),
                $loaded[$i]['value']->item()
            );
            $this->assertEquals(
                $original[$i]['ratio']->item(),
                $loaded[$i]['ratio']->item()
            );
        }
    }

    // Aggregations on loaded CSV

    public function testCsvAggregations(): void
    {
        $df = DataFrame::fromCsv(self::FIXTURES_DIR . '/numeric.csv');

        // Sum: a=15, b=150, c=1500
        $sum = $df->select([\Polars\Expr::col('a')->sum()]);
        $this->assertEquals(15, $sum->item());

        // Min
        $min = $df->min();
        $this->assertEquals(1, $min['a']->item());
        $this->assertEquals(10, $min['b']->item());
        $this->assertEquals(100, $min['c']->item());

        // Max
        $max = $df->max();
        $this->assertEquals(5, $max['a']->item());
        $this->assertEquals(50, $max['b']->item());
        $this->assertEquals(500, $max['c']->item());

        // Mean
        $mean = $df->mean();
        $this->assertEquals(3.0, $mean['a']->item());
        $this->assertEquals(30.0, $mean['b']->item());
        $this->assertEquals(300.0, $mean['c']->item());
    }

    public function testCsvWithHeadTail(): void
    {
        $df = DataFrame::fromCsv(self::FIXTURES_DIR . '/simple.csv');

        $head = $df->head(2);
        $this->assertEquals(2, $head->height());
        $this->assertEquals('Alice', $head[0]['name']->item());
        $this->assertEquals('Bob', $head[1]['name']->item());

        $tail = $df->tail(2);
        $this->assertEquals(2, $tail->height());
        $this->assertEquals('Diana', $tail[0]['name']->item());
        $this->assertEquals('Eve', $tail[1]['name']->item());
    }
}
