<?php

namespace App\Servers;

use Swoole\WebSocket\Server;

class WebSocketServer
{
    protected array $rooms = [];

    public function __construct()
    {
        $this->startServer();
    }

    /**
     * @return void
     */
    private function startServer(): void
    {
        $server = new Server('0.0.0.0', 9090);
        $server->set($this->getConfig());
        
        $server->on('Open', function (Server $server, $request){
            $this->onOpen($server, $request);
        });
        
        $server->on('Close', function (Server $server, $request){
            $this->onClose($server, $request);
        });
        
        $server->on('Message', function (Server $server, $frame){
            $this->onMessage($server, $frame);
        });
        
        $server->start();
    }

    /**
     * @return array
     */
    protected function getConfig(): array
    {
        return [
            'debug_mode'     => true,
            'display_errors' => true,
            'log_file'       => '/dev/stdout',
            //'worker_num'     => 4
        ];
    }

    /**
     * @param Server $server
     * @param [type] $request
     * @return void
     */
    protected function onOpen(Server $server, $request): void
    {
        echo "[ws] client-{$request->fd} is connected" . PHP_EOL;
        $server->push($request->fd, 'Hello, welcome!');
    }

    /**
     * Undocumented function
     *
     * @param Server $server
     * @param [type] $fd
     * @return void
     */
    protected function onClose(Server $server, $fd): void
    {
        echo "[ws] client-{$fd} is closed" . PHP_EOL;

        foreach ($this->rooms as &$room) {
            if (isset($room[$fd])) {
                unset($room[$fd]);
            }
        }
    }

    /**
     * @param Server $server
     * @param [type] $frame
     * @return void
     */
    protected function onMessage(Server $server, $frame): void
    {
        echo "[ws] message received: {$frame->data}" . PHP_EOL;
        
        $data = json_decode($frame->data, true);
        $message = $data['message'] ?? null;
        $action = $data['action'] ?? null;
        $room = $data['room'] ?? null;

        match ($action) {
            'join'      => $this->joinRoom($server, $frame->fd, $room),
            'leave'     => $this->leaveRoom($server, $frame->fd, $room),
            'message'   => $this->sendMessage($server, $frame->fd, $room, $message),
            'broadcast' => $this->broadcastMessage($server, $frame->fd, $message),
            default     => $server->push($frame->fd, "Unknown action: {$action}"),
        };
    }

    /**
     * @param Server $server
     * @param integer $fd
     * @param string $room
     * @return void
     */
    protected function joinRoom(Server $server, int $fd, ?string $room): void
    {
        if ($room === null) {
            $server->push($fd, 'Room name is required to join a room');
            return;
        }

        if (!isset($this->rooms[$room])) {
            $this->rooms[$room] = [];
        }

        $this->rooms[$room][$fd] = $fd;
        $server->push($fd, "Joined room: {$room}");
    }

    /**
     * @param Server $server
     * @param integer $fd
     * @param string|null $room
     * @return void
     */
    protected function leaveRoom(Server $server, int $fd, ?string $room): void
    {
        if ($room === null) {
            $server->push($fd, 'Room name is required to leave a room');
            return;
        }

        if (isset($this->rooms[$room])) {
            unset($this->rooms[$room][$fd]);
            $server->push($fd, "Left room: {$room}");
        }
    }

    /**
     * @param Server $server
     * @param integer $fd
     * @param string|null $room
     * @param string|null $message
     * @return void
     */
    protected function sendMessage(Server $server, int $fd, ?string $room, ?string $message): void
    {
        if ($room === null) {
            $server->push($fd, 'Room name is required to send a message');
            return;
        }

        if ($message === null) {
            $server->push($fd, 'Message content is required to send a message');
            return;
        }

        if(!isset($this->rooms[$room][$fd])) {
            $server->push($fd, 'It is require to join the room before sending a message');
            return;
        }

        foreach ($this->rooms[$room] as $fdInRoom) {
            if ($server->isEstablished($fdInRoom)) {
                $server->push($fdInRoom, "[{$room}] client-{$fd}: {$message}");
            }
        }
    }

    /**
     * @param Server $server
     * @param integer $fd
     * @param string|null $message
     * @return void
     */
    protected function broadcastMessage(Server $server, int $fd, ?string $message): void
    {
        if ($message === null) {
            $server->push($fd, 'Message content is required to broadcast');
            return;
        }

        foreach ($server->connections as $fd) {
            if ($server->isEstablished($fd)) {
                $server->push($fd, "Broadcast: {$message}");
            }
        }
    }
}