<?php

namespace App\Controllers;

use App\AsyncTasks\AsyncTaskExample;
use Swoole\Coroutine;

class ExamplesController extends Controller
{
    public function asyncTask(): string
    {
        // Your logic

        // Dispatch async task
        $this->application->server->task(new AsyncTaskExample());

        return 'Ok';
    }

    public function blockedCoroutines(): array
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
}