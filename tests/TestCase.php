<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Faker\Factory;
use Faker\Generator;

class TestCase extends BaseTestCase
{
    protected Generator $faker;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->faker = Factory::create();
    }

    public function storagePath(string $filePath): string
    {
        return __DIR__ . '/../storage/' . $filePath;
    }
}