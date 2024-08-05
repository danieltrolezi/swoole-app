<?php

namespace App\Servers;

use App\Controllers\TaskController;
use App\AsyncTasks\AsyncTaskInterface;
use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server\Task;

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
        $this->server->on('Request', function (Request $request, Response $response) {
            $this->onRequest($request, $response);
        });
        $this->server->on('Task', function(Server $server, Task $task){
            $this->onTask($server, $task);
        });

        $this->server->start();
    }

    /**
     * @return array
     */
    protected function getConfig(): array
    {
        return [
            'debug_mode'            => true,
            'display_errors'        => true,
            'log_file'              => '/dev/stdout',
            'open_http2_protocol'   => true,
            'task_enable_coroutine' => true,
            'worker_num'            => 4,
            'task_worker_num'       => 4,
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

        if ($request->server['request_uri'] == '/examples/async-task') {
            $controller = new TaskController($this->server);
            $controller->asyncTask();
            
            $response->end('Ok');
            return;
        }

        $response->header('Content-Type', 'text/html; charset=utf-8');
        $response->end('<h1>Hello Swoole. #' . rand(1000, 9999) . '</h1>');
    }

    protected function onTask(Server $server, Task $task)
    {
        echo "[http] new AsyncTask[id={$task->id}, name={$task->data->name()}]" . PHP_EOL;

        if (!$task->data instanceof AsyncTaskInterface) {
            throw new \Exception('$task->data must implement AsyncTaskInterface');
        }

        $task->data->process();

        echo "[http] finished AsyncTask[id={$task->id}, name={$task->data->name()}]" . PHP_EOL;
    }
}