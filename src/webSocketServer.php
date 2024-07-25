<?php

namespace App;

use Swoole\WebSocket\Server;

$ws = new Server('0.0.0.0', 9090);

$ws->set([
    'debug_mode'     => true,
    'display_errors' => true,
    'log_file'       => '/dev/stdout',
    'worker_num'     => 4
]);

$ws->on('Open', function ($ws, $request) {
    echo "[ws] client-{$request->fd} is connected" . PHP_EOL;
    $ws->push($request->fd, 'Hello, welcome!');
});

$ws->on('Message', function ($ws, $frame) {
    echo "[ws] message received: {$frame->data}" . PHP_EOL;

    $data = json_decode($frame->data, true);
    $sender = $frame->fd;
    $recipient = $data['recipient'] ?? 0;

    if(!empty($recipient)) {
        $ws->push($recipient, $data['message']);
        $ws->push($sender, 'Message sent successfully');
    } else {
        $ws->push($sender, $data['message']);
    }
});

$ws->on('Close', function ($ws, $fd) {
    echo "[ws] client-{$fd} is closed" . PHP_EOL;
});

$ws->start();