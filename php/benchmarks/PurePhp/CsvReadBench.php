<?php

namespace PolarsPhpBench\PurePhp;

use PhpBench\Attributes as Bench;
use PolarsPhpBench\Fixtures\DataGenerator;

/**
 * Read CSV using fgetcsv() into an array of associative arrays (row-based).
 */
class CsvReadBench
{
    private string $csvPath;

    public function setUp(array $params): void
    {
        $this->csvPath = DataGenerator::generateCsv($params['rows']);
    }

    public function provideSizes(): \Generator
    {
        foreach (DataGenerator::SIZES as $size) {
            yield sprintf('%d rows', $size) => ['rows' => $size];
        }
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\ParamProviders(['provideSizes'])]
    #[Bench\Revs(10)]
    #[Bench\Iterations(5)]
    #[Bench\Warmup(1)]
    public function benchReadCsv(): void
    {
        $fp = fopen($this->csvPath, 'r');
        $header = fgetcsv($fp);
        $rows = [];
        while (($row = fgetcsv($fp)) !== false) {
            $rows[] = array_combine($header, $row);
        }
        fclose($fp);
    }
}
