<?php

namespace App;

use Swoole\Http\Server;

$http = new Server('0.0.0.0', 8080);

$http->set([
    'debug_mode'     => true,
    'display_errors' => true,
    'log_file'       => '/dev/stdout',
    'worker_num'     => 4
]);

$http->on('Request', function ($request, $response) {
    if ($request->server['path_info'] == '/favicon.ico' 
        || $request->server['request_uri'] == '/favicon.ico'
    ) {
        $response->end();
        return;
    }

    $response->header('Content-Type', 'text/html; charset=utf-8');
    $response->end('<h1>Hello Swoole. #' . rand(1000, 9999) . '</h1>');

    //list($controller, $action) = explode('/', trim($request->server['request_uri'], '/'));
    // Map to different controller class and method based on $controller, $action.
    //(new $controller)->$action($request, $response);
});

$http->start();