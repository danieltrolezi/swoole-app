<?php

namespace App\Controllers;

use App\Services\NotificationService;

class ExampleController
{
    private NotificationService $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService();
    }

    public function processLongTask()
    {
        $this->notificationService->sendPushNotification(
            'This is a push notification!'
        );
    }
}