<?php

use App\Services\YamlReader;

require_once __DIR__ . '/../vendor/autoload.php';

if (empty($argv[1])) {
    echo 'Please provide a pipeline file.' . PHP_EOL;
    exit(1);
}

$reader = new YamlReader();
print_r($reader->read($argv[1]));