<?php

namespace App\Servers;

use App\Router;
use App\AsyncTasks\AsyncTaskInterface;
use App\Exceptions\NotFoundException;
use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server\Task;

class HttpServer
{
    private static ?HttpServer $instance = null;
    private Router $router;
    public Server $server;

    private function __construct()
    {
    }

    public static function getInstance(): HttpServer
    {
        if (self::$instance === null) {
            self::$instance = new HttpServer();
            self::$instance->setRouter();   
            self::$instance->startServer();
        }
        
        return self::$instance;
    }

    private function setRouter()
    {
        $this->router = new Router(
            require __DIR__ . '/../../config/routes.php'
        );
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
        $path = $request->server['request_uri'];
        $response = $this->setAccessControlHeaders($response);
        
        if ($path == '/favicon.ico') {
            $response->end();
            return;
        }

        try {
            $message = $this->router->dispatch($path);
            $status = 200;
        } catch (NotFoundException $e) {
            $message = $e->getMessage();
            $status = 404;
        }

        $this->responseJson($response, ['message' => $message], $status);
    }

    /**
     * @param Response $response
     * @return Response
     */
    private function setAccessControlHeaders(Response $response): Response
    {
        $response->header("Access-Control-Allow-Origin", "http://localhost");
        $response->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS");
        $response->header("Access-Control-Allow-Headers", "Content-Type, Authorization, X-Requested-With");

        return $response;
    }

    /**
     * @param Response $response
     * @param array $result
     * @param integer $status
     * @return void
     */
    private function responseJson(Response $response, array $result, int $status = 200)
    {
        $response->header("Content-Type", "application/json");
        $response->status($status);
        $response->end(json_encode($result));
    }

    /**
     * @param Server $server
     * @param Task $task
     * @return void
     */
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