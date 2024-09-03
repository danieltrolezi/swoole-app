<?php

namespace Tests\Unit\Services;

use App\Services\PipelineValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PipelineValidatorTest extends TestCase
{
    private PipelineValidator $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new PipelineValidator;
    }
    
    public function test_should_return_valid()
    {
        $filePath = $this->storagePath('pipelines/valid_pipeline.json');
        $result = $this->validator->validate($filePath);

        $this->assertEquals('VALID', $result);
    }

    public static function provider_invalid_files(): array
    {
        return [
            ['invalid_pipeline_no_name.json'],
            ['invalid_pipeline_no_jobs.json'],
            ['invalid_pipeline_job_no_command.json'],
            ['invalid_pipeline_job_no_name.json'],
            ['invalid_pipeline_job_invalid_depency_format.json'],
            ['invalid_pipeline_job_depency_not_found.json'],
            ['invalid_file.json'],
            ['invalid_pipeline_depends_on_itself.json'],
            ['invalid_pipeline_dependecy_cycle.json']
        ];
    }

    #[DataProvider('provider_invalid_files')]
    public function test_should_return_invalid(string $fileName)
    {
        $filePath = $this->storagePath("pipelines/$fileName");
        $result = $this->validator->validate($filePath);

        $this->assertEquals('INVALID', $result);
    }
}