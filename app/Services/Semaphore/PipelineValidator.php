<?php

namespace App\Services\Semaphore;

use Exception;

class PipelineValidator
{
    private const string VALID = 'VALID';
    private const string INVALID = 'INVALID';
    private ?array $pipeline;
    
    public function validate(string $filePath): string
    {
        try {
            $this->parsePipeline($filePath);
            $this->validateName();
            $this->validateJobs();
        } catch (Exception $e) {
            //echo $e->getMessage() . PHP_EOL;
            return self::INVALID;
        }

        return self::VALID;
    }

    private function parsePipeline(string $filePath): void
    {
        if(!file_exists($filePath)) {
            throw new Exception('File not found');
        }

        $this->pipeline = json_decode(
            file_get_contents($filePath),
            true
        );

        if(empty($this->pipeline)) {
            throw new Exception('Invalid JSON');
        }
    }

    private function validateName(): void
    {
        $this->validateString($this->pipeline['name'], 'Pipeline name');
    }

    private function validateJobs(): void
    {
        if(empty($this->pipeline['jobs'])) {
            throw new Exception('Pipeline must have at least one job');
        }

        foreach ($this->pipeline['jobs'] as $job) {
            $this->validateJob($job);
        }
    }

    private function validateJob(array $job): void
    {
        $this->validateString($job['name'], 'Job name');

        if(empty($job['commands']) || !is_array($job['commands'])) {
            throw new Exception('Job must have at least one command');
        }

        $this->validateDepencies($job);
    }  

    private function validateDepencies(array $job, array $dependents = []): void
    {
        if(empty($job['depends_on'])) {
            return;
        }

        if(!is_array($job['depends_on'])) {
            throw new Exception('depends_on must be an array');
        }

        $dependents[] = $job['name'];

        foreach ($job['depends_on'] as $dependency) {
            if($dependency === $job['name']) {
                throw new Exception('Job cannot depend on itself');
            }

            if(in_array($dependency, $dependents)) {
                throw new Exception('Circular dependency detected');
            }

            $this->validateDepencies(
                $this->getJobByName($dependency),
                $dependents
            );
        }
    }

    private function validateString(mixed $string, string $field): void
    {
        if(!is_string($string)) {
            throw new Exception("$field is not a valid string");
        }

        if(empty($string)) {
            throw new Exception("$field is required");
        }
    }

    private function getJobByName(string $name): array
    {
        $jobs = array_filter(
            $this->pipeline['jobs'],
            fn($job) => $job['name'] === $name
        );

        if(empty($jobs)) {
            throw new Exception('Job not found');
        }

        return reset($jobs);
    }
}