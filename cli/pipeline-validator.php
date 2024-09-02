<?php

use App\Services\Semaphore\PipelineValidator;

require_once __DIR__ . '/../vendor/autoload.php';

if (empty($argv[1])) {
    echo 'Please provide a pipeline file.' . PHP_EOL;
    exit(1);
}

$validator = new PipelineValidator();
echo $validator->validate($argv[1]) . PHP_EOL;