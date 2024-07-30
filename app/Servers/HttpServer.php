<?php

namespace App\Servers;

use Swoole\Http\Server;
use Swoole\Coroutine\Http\Client;

class HttpServer
{
    public function __construct()
    {
        $http = new Server('0.0.0.0', 8080);

        $http->set([
            'debug_mode'          => true,
            'display_errors'      => true,
            'log_file'            => '/dev/stdout',
            //'worker_num'          => 4,
            'open_http2_protocol' => true,
        ]);

        $http->on('Request', function ($request, $response) {
            if ($request->server['request_uri'] == '/favicon.ico') {
                $response->end();
                return;
            }

            if ($request->server['request_uri'] == '/examples/chat') {
                $client = new Client('localhost', 9090);
                $ret = $client->upgrade('/');
                
                if ($ret) {
                    //echo '[http] Connection upgraded to WebSocket' . PHP_EOL;
                    echo "[http] {$client->recv(1)->data}" . PHP_EOL;

                    $client->push(
                        json_encode([
                            'recipient' => 1,
                            'message'   => $request->post['message']
                        ])
                    );

                    echo "[http] {$client->recv(1)->data}" . PHP_EOL;
                } else {
                    echo '[http] Error upgrading connection to WebSocket' . PHP_EOL;
                }

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
    }
}