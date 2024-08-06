<?php 

namespace App\AsyncTasks;

use App\Services\NotificationService;
use Swoole\Coroutine;

class AsyncTaskExample implements AsyncTaskInterface 
{
    /**
     * @return string
     */
    public function name(): string
    {
        return 'async-task-example';
    }

    /**
     * @return void
     */
    public function process(): void
    {
        Coroutine::sleep(3);

        $notificationService = new NotificationService();
        $notificationService->sendPushNotification(
            'This is a push notification!'
        );
    }
}