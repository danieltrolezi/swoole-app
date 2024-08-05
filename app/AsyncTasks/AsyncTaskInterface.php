<?php

namespace App\AsyncTasks;

interface AsyncTaskInterface
{
    public function name(): string;
    public function process(): void;
}
