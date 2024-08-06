<?php

return [
    '/'                            => [App\Controllers\IndexController::class, 'home'],
    '/examples/async-task'         => [App\Controllers\ExamplesController::class, 'asyncTask'],
    '/examples/blocked-coroutines' => [App\Controllers\ExamplesController::class, 'blockedCoroutines']
];