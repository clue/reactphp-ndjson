<?php

// $ php examples/validate.php < examples/users.ndjson

use Clue\React\NDJson\Decoder;
use Clue\React\NDJson\Encoder;
use React\EventLoop\Loop;
use React\Stream\ReadableResourceStream;
use React\Stream\WritableResourceStream;

require __DIR__ . '/../vendor/autoload.php';

$exit = 0;
$in = new ReadableResourceStream(STDIN);
$out = new WritableResourceStream(STDOUT);
$info = new WritableResourceStream(STDERR);

$decoder = new Decoder($in);
$encoder = new Encoder($out);
$decoder->pipe($encoder);

$decoder->on('error', function (Exception $e) use ($info, &$exit) {
    $info->write('ERROR: ' . $e->getMessage() . PHP_EOL);
    $exit = 1;
});

$info->write('You can pipe/write a valid NDJson stream to STDIN' . PHP_EOL);
$info->write('Valid NDJson will be forwarded to STDOUT' . PHP_EOL);
$info->write('Invalid NDJson will raise an error on STDERR and exit with code 1' . PHP_EOL);

Loop::run();

exit($exit);
