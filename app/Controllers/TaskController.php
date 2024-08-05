<?php

namespace App\Controllers;

use App\AsyncTasks\AsyncTaskExample;
use Swoole\Http\Server;

class TaskController
{
    public function __construct(
        private Server $server
    )
    {
    }

    public function asyncTask(): void
    {
        // Your logic

        // Dispatch async task
        $this->server->task(new AsyncTaskExample());
    }
}