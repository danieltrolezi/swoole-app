<?php

/**
 * Simple example sharing data between two Coroutines using Channel.
 * 
 * More info: https://wiki.swoole.com/en/#/coroutine/channel
 * 
 * Execute by running on terminal: 
 * $ docker exec -it swoole-app-app-1 bash
 * $ php <PATH_TO_FILE>/index.php
 */

use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

require_once __DIR__ . '/../../../vendor/autoload.php';

Coroutine\run(function() {
    $channel = new Channel(1);

    go(function () use ($channel) {
        $coroutineId = Coroutine::getCid();

        for($i = 0; $i < 10; $i++) {
            Coroutine::sleep(1.0);
            $channel->push(['rand' => rand(1000, 9999), 'index' => $i]);
            echo "[co-{$coroutineId}] Finished index {$i}"   . PHP_EOL;
        }
    });

    go(function () use ($channel) {
        $coroutineId = Coroutine::getCid();

        while(1) {
            $data = $channel->pop(2.0);
            $length = $channel->length();

            if ($data !== false) {
                echo "[co-{$coroutineId}] Data: " . json_encode([
                    'data'   => $data,
                    'length' => $length
                ])  . PHP_EOL;
            } else {
                if($channel->errCode !== SWOOLE_CHANNEL_TIMEOUT) {
                    echo "[co-{$coroutineId}] Error: {$channel->errCode}"  . PHP_EOL;
                }

                break;
            }
        }
    });
});