<?php

namespace App\Servers;

use Swoole\WebSocket\Server;

class WebSocketServer
{
    private Server $server;
    private array $rooms = [];

    public function __construct()
    {
        $this->server = new Server('0.0.0.0', 9090);

        $this->server->set([
            'debug_mode'     => true,
            'display_errors' => true,
            'log_file'       => '/dev/stdout',
            //'worker_num'     => 4
        ]);

        $this->server->on('Open', function ($ws, $request) {
            echo "[ws] client-{$request->fd} is connected" . PHP_EOL;
            $ws->push($request->fd, 'Hello, welcome!');
        });

        $this->server->on('Close', function ($ws, $fd) {
            echo "[ws] client-{$fd} is closed" . PHP_EOL;
        });

        $this->server->on('Message', function ($ws, $frame) {
            echo "[ws] message received: {$frame->data}" . PHP_EOL;
        
            $data = json_decode($frame->data, true);
            $action = $data['action'] ?: '';
            $room = $data['room'] ?: '';
        
            switch ($action) {
                case 'join':
                    if (!isset($this->rooms[$room])) {
                        $this->rooms[$room] = [];
                    }
                    $this->rooms[$room][$frame->fd] = $frame->fd;
                    $ws->push($frame->fd, "Joined room: {$room}");
                    break;
    
                case 'leave':
                    if (isset($this->rooms[$room])) {
                        unset($this->rooms[$room][$frame->fd]);
                        $ws->push($frame->fd, "Left room: {$room}");
                    }
                    break;
    
                case 'message':
                    if (isset($this->rooms[$room]) && isset($this->rooms[$room][$frame->fd])) {
                        foreach ($this->rooms[$room] as $fd) {
                            if ($ws->isEstablished($fd)) {
                                $ws->push($fd, "[{$room}] client-{$frame->fd}: {$data['message']}");
                            }
                        }
                    }
                    break;

                case 'broadcast':
                    foreach ($this->server->connections as $fd) {
                        if ($this->server->isEstablished($fd)) {
                            $this->server->push($fd, "Broadcast: {$data['message']}");
                        }
                    }
                    break;
    
                default:
                    $ws->push($frame->fd, "Unknown action: {$action}");
                    break;
            }
        });

        $this->server->start();
    }
}