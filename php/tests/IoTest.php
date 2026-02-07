<?php

namespace Tests\Polars;

use Exception;
use PHPUnit\Framework\TestCase;
use Polars\DataFrame;
use Polars\Expr;
use Polars\LazyFrame;

class IoTest extends TestCase
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
        $patterns = ['*.json', '*.ndjson', '*.parquet'];
        foreach ($patterns as $pattern) {
            $files = glob(self::OUTPUT_DIR . '/' . $pattern);
            foreach ($files as $file) {
                unlink($file);
            }
        }
        if (is_dir(self::OUTPUT_DIR) && count(glob(self::OUTPUT_DIR . '/*')) === 0) {
            rmdir(self::OUTPUT_DIR);
        }
    }

    private function createTestDataFrame(): DataFrame
    {
        return new DataFrame([
            'name' => ['Alice', 'Bob', 'Charlie'],
            'age' => [25, 30, 35],
            'salary' => [50000, 60000, 75000],
        ]);
    }

    // readCsv //

    public function testReadCsv(): void
    {
        $df = DataFrame::readCsv(self::FIXTURES_DIR . '/simple.csv');

        $this->assertInstanceOf(DataFrame::class, $df);
        $this->assertEquals(5, $df->height());
        $this->assertEquals(4, $df->width());
        $this->assertEquals(['name', 'age', 'city', 'salary'], $df->columns);
    }

    public function testReadCsvWithOptions(): void
    {
        $df = DataFrame::readCsv(self::FIXTURES_DIR . '/semicolon.csv', separator: ';');

        $this->assertInstanceOf(DataFrame::class, $df);
        $this->assertEquals(3, $df->height());
    }

    // JSON //

    public function testReadJson(): void
    {
        $df = DataFrame::readJson(self::FIXTURES_DIR . '/simple.json');

        $this->assertInstanceOf(DataFrame::class, $df);
        $this->assertEquals(5, $df->height());
        $this->assertEquals(4, $df->width());
    }

    public function testReadJsonValues(): void
    {
        $df = DataFrame::readJson(self::FIXTURES_DIR . '/simple.json');

        $this->assertEquals('Alice', $df[0]['name']->item());
        $this->assertEquals(25, $df[0]['age']->item());
        $this->assertEquals('Eve', $df[-1]['name']->item());
    }

    public function testReadJsonNonExistent(): void
    {
        $this->expectException(Exception::class);
        DataFrame::readJson(self::FIXTURES_DIR . '/nonexistent.json');
    }

    public function testWriteJsonRoundTrip(): void
    {
        $original = $this->createTestDataFrame();
        $outputPath = self::OUTPUT_DIR . '/roundtrip.json';

        $original->writeJson($outputPath);
        $this->assertFileExists($outputPath);

        $loaded = DataFrame::readJson($outputPath);
        $this->assertEquals($original->height(), $loaded->height());
        $this->assertEquals($original->width(), $loaded->width());
        $this->assertEquals($original->columns, $loaded->columns);

        for ($i = 0; $i < $original->height(); $i++) {
            $this->assertEquals(
                $original[$i]['name']->item(),
                $loaded[$i]['name']->item()
            );
            $this->assertEquals(
                $original[$i]['age']->item(),
                $loaded[$i]['age']->item()
            );
        }
    }

    // NDJSON //

    public function testReadNdjson(): void
    {
        $df = DataFrame::readNdjson(self::FIXTURES_DIR . '/simple.ndjson');

        $this->assertInstanceOf(DataFrame::class, $df);
        $this->assertEquals(5, $df->height());
        $this->assertEquals(4, $df->width());
    }

    public function testReadNdjsonValues(): void
    {
        $df = DataFrame::readNdjson(self::FIXTURES_DIR . '/simple.ndjson');

        $this->assertEquals('Alice', $df[0]['name']->item());
        $this->assertEquals(25, $df[0]['age']->item());
        $this->assertEquals('Eve', $df[-1]['name']->item());
    }

    public function testReadNdjsonNonExistent(): void
    {
        $this->expectException(Exception::class);
        DataFrame::readNdjson(self::FIXTURES_DIR . '/nonexistent.ndjson');
    }

    public function testWriteNdjsonRoundTrip(): void
    {
        $original = $this->createTestDataFrame();
        $outputPath = self::OUTPUT_DIR . '/roundtrip.ndjson';

        $original->writeNdjson($outputPath);
        $this->assertFileExists($outputPath);

        $loaded = DataFrame::readNdjson($outputPath);
        $this->assertEquals($original->height(), $loaded->height());
        $this->assertEquals($original->width(), $loaded->width());
        $this->assertEquals($original->columns, $loaded->columns);

        for ($i = 0; $i < $original->height(); $i++) {
            $this->assertEquals(
                $original[$i]['name']->item(),
                $loaded[$i]['name']->item()
            );
            $this->assertEquals(
                $original[$i]['age']->item(),
                $loaded[$i]['age']->item()
            );
        }
    }

    // Parquet //

    public function testWriteReadParquetRoundTrip(): void
    {
        $original = $this->createTestDataFrame();
        $outputPath = self::OUTPUT_DIR . '/roundtrip.parquet';

        $original->writeParquet($outputPath);
        $this->assertFileExists($outputPath);

        $loaded = DataFrame::readParquet($outputPath);
        $this->assertEquals($original->height(), $loaded->height());
        $this->assertEquals($original->width(), $loaded->width());
        $this->assertEquals($original->columns, $loaded->columns);

        for ($i = 0; $i < $original->height(); $i++) {
            $this->assertEquals(
                $original[$i]['name']->item(),
                $loaded[$i]['name']->item()
            );
            $this->assertEquals(
                $original[$i]['age']->item(),
                $loaded[$i]['age']->item()
            );
        }
    }

    public function testReadParquetNonExistent(): void
    {
        $this->expectException(Exception::class);
        DataFrame::readParquet(self::FIXTURES_DIR . '/nonexistent.parquet');
    }

    // Scan methods //

    public function testScanCsv(): void
    {
        $lf = LazyFrame::scanCsv(self::FIXTURES_DIR . '/simple.csv');

        $this->assertInstanceOf(LazyFrame::class, $lf);

        $df = $lf->collect();
        $this->assertEquals(5, $df->height());
        $this->assertEquals(4, $df->width());
        $this->assertEquals(['name', 'age', 'city', 'salary'], $df->columns);
    }

    public function testScanCsvWithFilter(): void
    {
        $df = LazyFrame::scanCsv(self::FIXTURES_DIR . '/simple.csv')
            ->filter(Expr::col('age')->gt(30))
            ->collect();

        $this->assertEquals(2, $df->height());
    }

    public function testScanCsvWithOptions(): void
    {
        $lf = LazyFrame::scanCsv(self::FIXTURES_DIR . '/semicolon.csv', separator: ';');

        $df = $lf->collect();
        $this->assertEquals(3, $df->height());
        $this->assertEquals(['product', 'price', 'quantity'], $df->columns);
    }

    public function testScanNdjson(): void
    {
        $lf = LazyFrame::scanNdjson(self::FIXTURES_DIR . '/simple.ndjson');

        $this->assertInstanceOf(LazyFrame::class, $lf);

        $df = $lf->collect();
        $this->assertEquals(5, $df->height());
        $this->assertEquals(4, $df->width());
    }

    public function testScanNdjsonWithFilter(): void
    {
        $df = LazyFrame::scanNdjson(self::FIXTURES_DIR . '/simple.ndjson')
            ->filter(Expr::col('salary')->ge(60000))
            ->collect();

        $this->assertGreaterThanOrEqual(1, $df->height());
    }

    public function testScanParquet(): void
    {
        // First create a parquet file
        $original = $this->createTestDataFrame();
        $parquetPath = self::OUTPUT_DIR . '/scan_test.parquet';
        $original->writeParquet($parquetPath);

        $lf = LazyFrame::scanParquet($parquetPath);

        $this->assertInstanceOf(LazyFrame::class, $lf);

        $df = $lf->collect();
        $this->assertEquals(3, $df->height());
        $this->assertEquals(3, $df->width());
    }

    public function testScanParquetWithFilter(): void
    {
        $original = $this->createTestDataFrame();
        $parquetPath = self::OUTPUT_DIR . '/scan_filter_test.parquet';
        $original->writeParquet($parquetPath);

        $df = LazyFrame::scanParquet($parquetPath)
            ->filter(Expr::col('age')->gt(25))
            ->collect();

        $this->assertEquals(2, $df->height());
    }

    // Sink methods //

    public function testSinkCsv(): void
    {
        $original = $this->createTestDataFrame();
        $outputPath = self::OUTPUT_DIR . '/sink_test.csv';

        $result = $original->lazy()->sinkCsv($outputPath);
        $this->assertInstanceOf(DataFrame::class, $result);
        $this->assertFileExists($outputPath);

        $loaded = DataFrame::readCsv($outputPath);
        $this->assertEquals($original->height(), $loaded->height());
        $this->assertEquals($original->columns, $loaded->columns);
    }

    public function testSinkCsvWithOptions(): void
    {
        $original = $this->createTestDataFrame();
        $outputPath = self::OUTPUT_DIR . '/sink_semicolon.csv';

        $original->lazy()->sinkCsv($outputPath, separator: ';');
        $this->assertFileExists($outputPath);

        $loaded = DataFrame::readCsv($outputPath, separator: ';');
        $this->assertEquals($original->height(), $loaded->height());
    }

    public function testSinkParquet(): void
    {
        $original = $this->createTestDataFrame();
        $outputPath = self::OUTPUT_DIR . '/sink_test.parquet';

        $result = $original->lazy()->sinkParquet($outputPath);
        $this->assertInstanceOf(DataFrame::class, $result);
        $this->assertFileExists($outputPath);

        $loaded = DataFrame::readParquet($outputPath);
        $this->assertEquals($original->height(), $loaded->height());
        $this->assertEquals($original->columns, $loaded->columns);
    }

    public function testSinkNdjson(): void
    {
        $original = $this->createTestDataFrame();
        $outputPath = self::OUTPUT_DIR . '/sink_test.ndjson';

        $result = $original->lazy()->sinkNdjson($outputPath);
        $this->assertInstanceOf(DataFrame::class, $result);
        $this->assertFileExists($outputPath);

        $loaded = DataFrame::readNdjson($outputPath);
        $this->assertEquals($original->height(), $loaded->height());
        $this->assertEquals($original->columns, $loaded->columns);
    }

    public function testSinkWithTransformations(): void
    {
        $original = new DataFrame([
            'name' => ['Alice', 'Bob', 'Charlie', 'Diana'],
            'age' => [25, 30, 35, 28],
        ]);
        $outputPath = self::OUTPUT_DIR . '/sink_filtered.parquet';

        $original->lazy()
            ->filter(Expr::col('age')->gt(27))
            ->sinkParquet($outputPath);

        $loaded = DataFrame::readParquet($outputPath);
        $this->assertEquals(3, $loaded->height());
    }
}
