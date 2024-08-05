<?php 

namespace App\AsyncTasks;

use App\Services\NotificationService;

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
        sleep(3);

        $notificationService = new NotificationService();
        $notificationService->sendPushNotification(
            'This is a push notification!'
        );
    }
}