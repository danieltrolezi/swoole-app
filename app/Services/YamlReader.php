<?php

namespace App\Services;

class YamlReader
{
    public function read(string $filePath): array
    {
        return yaml_parse_file($filePath);
    }
}