<?php

use App\Servers\HttpServer;
use App\Servers\WebSocketServer;

require_once __DIR__ . '/../vendor/autoload.php';

match($argv[1]) {
    'http'      => new HttpServer(),
    'websocket' => new WebSocketServer(),
    default     => throw new \Exception('Invalid server type'),
};