<?php 

namespace App\Services;

use Swoole\Coroutine\Http\Client;

class NotificationService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client('localhost', 9090);
        $this->upgradeConnection();
        $this->joinPrivateRoom();
    }

    private function upgradeConnection()
    {
        $ret = $this->client->upgrade('/');
        
        if ($ret) {
            echo '[http] Connection upgraded to WebSocket' . PHP_EOL;
            echo $this->getClientResponse();
        } else {
            throw new \Exception('Failed to upgrade connection to WebSocket');
        }
    }

    private function joinPrivateRoom(): void
    {
        $this->client->push(
            json_encode([
                'action'    => 'join',
                'room'      => 'room-private'
            ])
        );

        echo $this->getClientResponse();
    }

    public function sendPushNotification(string $message): void
    {
        $this->client->push(
            json_encode([
                'action'    => 'message',
                'room'      => 'room-private',
                'message'   => $message
            ])
        );

        echo $this->getClientResponse();
    }

    private function getClientResponse(): string
    {
        return "[http] Retrieved data from server: {$this->client->recv(1)->data}" . PHP_EOL;
    }
}