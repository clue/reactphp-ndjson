<?php

// $ php examples/validate.php < examples/users.ndjson

use React\EventLoop\Loop;

require __DIR__ . '/../vendor/autoload.php';

$exit = 0;
$in = new React\Stream\ReadableResourceStream(STDIN);
$out = new React\Stream\WritableResourceStream(STDOUT);
$info = new React\Stream\WritableResourceStream(STDERR);

$ndjson = new Clue\React\NDJson\Decoder($in);
$encoder = new Clue\React\NDJson\Encoder($out);
$ndjson->pipe($encoder);

$ndjson->on('error', function (Exception $e) use ($info, &$exit) {
    $info->write('ERROR: ' . $e->getMessage() . PHP_EOL);
    $exit = 1;
});

$info->write('You can pipe/write a valid NDJson stream to STDIN' . PHP_EOL);
$info->write('Valid NDJson will be forwarded to STDOUT' . PHP_EOL);
$info->write('Invalid NDJson will raise an error on STDERR and exit with code 1' . PHP_EOL);

Loop::run();

exit($exit);
