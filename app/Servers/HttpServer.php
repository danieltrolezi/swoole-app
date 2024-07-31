<?php

namespace App\Servers;

use App\Controllers\ExampleController;
use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

class HttpServer
{
    private Server $server;

    public function __construct()
    {
        $this->startServer();
    }

    /**
     * @return void
     */
    private function startServer(): void
    {
        $this->server = new Server('0.0.0.0', 8080);
        $this->server->set($this->getConfig());
        $this->server->on('Request', function ($request, $response) {
            $this->onRequest($request, $response);
        });

        $this->server->start();
    }

    /**
     * @return array
     */
    protected function getConfig(): array
    {
        return [
            'debug_mode'          => true,
            'display_errors'      => true,
            'log_file'            => '/dev/stdout',
            //'worker_num'          => 4,
            'open_http2_protocol' => true,
        ];
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return void
     */
    protected function onRequest(Request $request, Response $response)
    {
        $response->header("Access-Control-Allow-Origin", "http://localhost");
        $response->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS");
        $response->header("Access-Control-Allow-Headers", "Content-Type, Authorization, X-Requested-With");

        if ($request->server['request_uri'] == '/favicon.ico') {
            $response->end();
            return;
        }

        if ($request->server['request_uri'] == '/examples/process-long-task') {
            $controller = new ExampleController();
            $controller->processLongTask();
            
            $response->end('Ok');
            return;
        }

        $response->header('Content-Type', 'text/html; charset=utf-8');
        $response->end('<h1>Hello Swoole. #' . rand(1000, 9999) . '</h1>');
    }
}