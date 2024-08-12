<?php

namespace App\Controllers;

use App\AsyncTasks\AsyncTaskExample;
use App\Services\NotificationService;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

class ExamplesController extends Controller
{
    /**
     * @return string
     */
    public function asyncTask(): string
    {
        // [Logic goes here]

        $this->application->server->task(new AsyncTaskExample());

        return 'Ok';
    }

    /**
     * @return array
     */
    public function blockingCoroutines(): array
    {
        $result = [];

        Coroutine::join([
            go(function() use(&$result) {
                Coroutine::sleep(3);
                $result[] = 'coroutine A';
            }),
            go(function() use(&$result) {
                Coroutine::sleep(1);
                $result[] = 'coroutine B';
            }),
            go(function() use(&$result) {
                Coroutine::sleep(2);
                $result[] = 'coroutine C';
            })
        ]);

        return $result;
    }

    /**
     * @return string
     */
    public function nonBlockingCoroutines(): string
    {
        /**
         * WARNING
         * 
         * if the Coroutines tries to call NotificationService::sendPushNotification
         * on the same instance, on the same time, it will throw an execption.
         * 
         * But for for teaching purposes it uses the same instance.
         */
        $notificationService = new NotificationService();

        go(function() use(&$notificationService) {
            Coroutine::sleep(3);
            $notificationService->sendPushNotification('coroutine A');
        });

        go(function() use(&$notificationService) {
            Coroutine::sleep(1);
            $notificationService->sendPushNotification('coroutine B');
        });

        go(function() use(&$notificationService) {
            Coroutine::sleep(2);
            $notificationService->sendPushNotification('coroutine C');
        });

        return 'Ok';
    }

    /**
     * @return string
     */
    public function channelCoroutines(): string
    {
        $channel = new Channel(1); 

        go(function () use ($channel) {
            $coroutineId = Coroutine::getCid();
            $notificationService = new NotificationService();

            for($i = 0; $i < 10; $i++) {
                Coroutine::sleep(1.0);
                $channel->push(['rand' => rand(1000, 9999), 'index' => $i]);
                
                $notificationService->sendPushNotification("[co-{$coroutineId}] Finished index {$i}");
            }
        });

        go(function () use ($channel) {
            $coroutineId = Coroutine::getCid();
            $notificationService = new NotificationService();
            
            while(1) {
                $data = $channel->pop(2.0);
                $length = $channel->length();

                if ($data !== false) {
                    $notificationService->sendPushNotification(
                            "[co-{$coroutineId}] Data: " . json_encode([
                            'data'   => $data,
                            'length' => $length
                        ])
                    );
                } else {
                    if($channel->errCode !== SWOOLE_CHANNEL_TIMEOUT) {
                        $notificationService->sendPushNotification("[co-{$coroutineId}] Error: {$channel->errCode}");
                    }

                    break;
                }
            }
        });

        return 'Ok';
    }
}