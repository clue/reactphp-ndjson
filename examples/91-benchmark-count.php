<?php

// simple usage:
// $ php examples/91-benchmark-count.php < examples/users.ndjson
//
// getting reasonable results requires a large data set:
// 1) download a large CSV/TSV dataset, for example:
// @link https://datasets.imdbws.com/
// @link https://github.com/fivethirtyeight/russian-troll-tweets
//
// 2) convert CSV/TSV to NDJSON, for example:
// @link https://github.com/clue/reactphp-csv/blob/v1.0.0/examples/11-csv2ndjson.php
//
// 3) pipe NDJSON into benchmark script:
// $ php examples/91-benchmark-count.php < title.ratings.ndjson

use Clue\React\NDJson\Decoder;
use React\EventLoop\Loop;
use React\Stream\ReadableResourceStream;

require __DIR__ . '/../vendor/autoload.php';

if (extension_loaded('xdebug')) {
    echo 'NOTICE: The "xdebug" extension is loaded, this has a major impact on performance.' . PHP_EOL;
}

$decoder = new Decoder(new ReadableResourceStream(STDIN), true);

$count = 0;
$decoder->on('data', function () use (&$count) {
    ++$count;
});

$start = microtime(true);
$report = Loop::addPeriodicTimer(0.05, function () use (&$count, $start) {
    printf("\r%d records in %0.3fs...", $count, microtime(true) - $start);
});

$decoder->on('close', function () use (&$count, $report, $start) {
    $now = microtime(true);
    Loop::cancelTimer($report);

    printf("\r%d records in %0.3fs => %d records/s\n", $count, $now - $start, $count / ($now - $start));
});
