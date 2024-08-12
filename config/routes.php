<?php

return [
    '/'                                 => [App\Controllers\IndexController::class, 'home'],
    '/examples/async-task'              => [App\Controllers\ExamplesController::class, 'asyncTask'],
    '/examples/blocking-coroutines'     => [App\Controllers\ExamplesController::class, 'blockingCoroutines'],
    '/examples/non-blocking-coroutines' => [App\Controllers\ExamplesController::class, 'nonBlockingCoroutines'],
    '/examples/channel-coroutines'      => [App\Controllers\ExamplesController::class, 'channelCoroutines']
];