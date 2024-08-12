<?php

/**
 * Simple Concurrency/Coroutine example in a script
 * Coroutine\run is used to initialize Coroutine Schedule/Context
 * and should be the start point of the application.
 * 
 * More info: https://wiki.swoole.com/en/#/start/coroutine
 * 
 * Execute by running "php main.php" on terminal
 */

use Swoole\Coroutine;

require_once __DIR__ . '/../../../vendor/autoload.php';

Coroutine\run(function() {
    go(function() {
        Coroutine::sleep(3);
        echo 'coroutine A' . PHP_EOL;
    });

    go(function() {
        Coroutine::sleep(1);
        echo 'coroutine B' . PHP_EOL;
    });

    go(function() {
        Coroutine::sleep(2);
        echo 'coroutine C' . PHP_EOL;
    });

    echo 'First line.' . PHP_EOL;
});

echo 'Last line.' . PHP_EOL;