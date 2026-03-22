<?php

/**
 * Benchmark comparison report: Polars-PHP vs Pure PHP
 *
 * Usage:
 *   php benchmarks/compare-report.php                    # Run benchmarks and show comparison
 *   php benchmarks/compare-report.php results.xml        # Use existing dump file
 */

$dumpFile = $argv[1] ?? null;

if ($dumpFile === null) {
    $dumpFile = sys_get_temp_dir() . '/polars_bench_' . uniqid() . '.xml';
    $phpbench = __DIR__ . '/../vendor/bin/phpbench';
    $config = __DIR__ . '/phpbench.json';

    fprintf(STDERR, "Running benchmarks...\n");
    $cmd = sprintf(
        '%s run --config=%s --report=default --dump-file=%s 2>&1',
        escapeshellarg($phpbench),
        escapeshellarg($config),
        escapeshellarg($dumpFile)
    );
    passthru($cmd, $exitCode);

    if ($exitCode !== 0) {
        fprintf(STDERR, "PHPBench exited with code %d\n", $exitCode);
        // Continue anyway — dump file may still have partial results
    }
    echo "\n";
}

if (!file_exists($dumpFile)) {
    fprintf(STDERR, "Dump file not found: %s\n", $dumpFile);
    exit(1);
}

$xml = simplexml_load_file($dumpFile);
if ($xml === false) {
    fprintf(STDERR, "Failed to parse XML dump file\n");
    exit(1);
}

// Parse results from XML
$results = [];

foreach ($xml->suite as $suite) {
    foreach ($suite->benchmark as $benchmark) {
        $class = (string)$benchmark['class'];

        // Determine if Polars or PurePhp
        if (str_contains($class, '\\Polars\\')) {
            $type = 'polars';
        } elseif (str_contains($class, '\\PurePhp\\')) {
            $type = 'purephp';
        } else {
            continue;
        }

        // Short benchmark name (e.g., "JoinBench")
        $parts = explode('\\', $class);
        $benchName = end($parts);

        foreach ($benchmark->subject as $subject) {
            $subjectName = (string)$subject['name'];

            foreach ($subject->variant as $variant) {
                // Variant name comes from parameter-set
                $paramSet = $variant->{'parameter-set'};
                $variantName = $paramSet ? (string)$paramSet['name'] : '';

                // Use pre-computed stats mode if available, otherwise compute from iterations
                $stats = $variant->stats;
                $memPeak = 0;

                if ($stats) {
                    $modeTime = (float)$stats['mode'];
                } else {
                    // Fallback: compute from iteration attributes
                    $times = [];
                    foreach ($variant->iteration as $iteration) {
                        $times[] = (float)$iteration['time-avg'];
                    }
                    if (empty($times)) {
                        continue;
                    }
                    sort($times);
                    $count = count($times);
                    $mid = intdiv($count, 2);
                    $modeTime = $count % 2 === 0
                        ? ($times[$mid - 1] + $times[$mid]) / 2
                        : $times[$mid];
                }

                // Memory from iteration attributes
                foreach ($variant->iteration as $iteration) {
                    $peak = (int)$iteration['mem-peak'];
                    if ($peak > $memPeak) {
                        $memPeak = $peak;
                    }
                }

                // modeTime is already per-rev (time-avg = time-net / revs)
                $key = $benchName . '::' . $subjectName . '|' . $variantName;

                $results[$key][$type] = [
                    'bench' => $benchName,
                    'subject' => $subjectName,
                    'variant' => $variantName,
                    'time' => $modeTime,             // microseconds per rev
                    'memory' => $memPeak,            // bytes
                ];
            }
        }
    }
}

// Build comparison table
$rows = [];
foreach ($results as $key => $pair) {
    if (!isset($pair['polars']) || !isset($pair['purephp'])) {
        continue;
    }

    $p = $pair['polars'];
    $pp = $pair['purephp'];

    // Format benchmark name: "JoinBench::benchJoinInner (100 rows)"
    $name = str_replace('Bench', '', $p['bench'])
        . '::' . str_replace('bench', '', $p['subject']);
    if ($p['variant']) {
        $name .= ' (' . $p['variant'] . ')';
    }

    if ($pp['time'] > 0 && $p['time'] > 0) {
        $ratio = $pp['time'] / $p['time'];
        if ($ratio == 1) {
            $timeFaster = '=';
        } elseif ($ratio > 1) {
            $timeFaster = round($ratio, 1) . 'x faster';
        } else {
            $timeFaster = round(1 / $ratio, 1) . 'x SLOWER';
        }
    } else {
        $timeFaster = 'N/A';
    }

    if ($pp['memory'] > 0 && $p['memory'] > 0) {
        $pct = (1 - $p['memory'] / $pp['memory']) * 100;
        if ($pct == 0) {
            $memBetter = '=';
        } elseif ($pct > 0) {
            $memBetter = round($pct, 1) . '% less';
        } else {
            $memBetter = round(-$pct, 1) . '% MORE';
        }
    } else {
        $memBetter = 'N/A';
    }

    $rows[] = [
        'name' => $name,
        'polars_time' => formatTime($p['time']),
        'php_time' => formatTime($pp['time']),
        'faster' => $timeFaster,
        'polars_mem' => formatMemory($p['memory']),
        'php_mem' => formatMemory($pp['memory']),
        'mem_better' => $memBetter,
    ];
}

if (empty($rows)) {
    echo "No matching Polars/PurePhp benchmark pairs found.\n";
    exit(0);
}

// Print markdown table
echo "| Benchmark | Polars Time | PHP Time | Speedup | Polars Mem | PHP Mem | Mem Saved |\n";
echo "| :--- | ---: | ---: | ---: | ---: | ---: | ---: |\n";

foreach ($rows as $row) {
    echo '| ' . $row['name']
        . ' | ' . $row['polars_time']
        . ' | ' . $row['php_time']
        . ' | ' . $row['faster']
        . ' | ' . $row['polars_mem']
        . ' | ' . $row['php_mem']
        . ' | ' . $row['mem_better']
        . " |\n";
}

// Cleanup temp dump file
if (!isset($argv[1]) && file_exists($dumpFile)) {
    unlink($dumpFile);
}

// --- Helper functions ---

function formatTime(float $microseconds): string
{
    if ($microseconds >= 1_000_000) {
        return round($microseconds / 1_000_000, 2) . 's';
    }
    if ($microseconds >= 1_000) {
        return round($microseconds / 1_000, 2) . 'ms';
    }
    return round($microseconds, 2) . 'μs';
}

function formatMemory(int $bytes): string
{
    if ($bytes >= 1_073_741_824) {
        return round($bytes / 1_073_741_824, 2) . 'GB';
    }
    if ($bytes >= 1_048_576) {
        return round($bytes / 1_048_576, 2) . 'MB';
    }
    if ($bytes >= 1024) {
        return round($bytes / 1024, 2) . 'KB';
    }
    return $bytes . 'B';
}
