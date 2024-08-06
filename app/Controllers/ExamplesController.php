<?php

namespace App\Controllers;

use App\AsyncTasks\AsyncTaskExample;
use App\Services\NotificationService;
use Swoole\Coroutine;

class ExamplesController extends Controller
{
    /**
     * @return string
     */
    public function asyncTask(): string
    {
        // Your logic

        // Dispatch async task
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
}