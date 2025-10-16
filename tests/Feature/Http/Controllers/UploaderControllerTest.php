<?php

namespace Tests\Feature\Http\Controllers;

use App\Imports\RowsImport;
use App\Models\ImportFile;
use App\Services\Contracts\FileStorageServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploaderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_uploads_a_valid_file_successfully(): void
    {
        Storage::fake('local');
        $mock = \Mockery::mock(FileStorageServiceProvider::class);
        $this->app->instance(FileStorageServiceProvider::class, $mock);

        $importFile = ImportFile::factory()->make([
            'id' => 1,
            'status' => 'pending',
        ]);

        $mock->shouldReceive('upload')
            ->once()
            ->andReturn($importFile);

        $file = $this->createTempXlsx(RowsImport::HEADERS);
        $response = $this->postJson('/upload', ['file' => $file]);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'status' => 'pending',
                    'original_name' => $file->getClientOriginalName(),
                ],
            ]);
    }

    public function test_it_fails_validation_if_file_missing_or_wrong_type(): void
    {
        $response = $this->postJson('/upload', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);

        $file = UploadedFile::fake()->create('text.txt', 10, 'text/plain');
        $response = $this->postJson('/upload', ['file' => $file]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }

    public function test_it_returns_error_if_storage_service_fails(): void
    {
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function (\Exception $exception) {
                return strpos($exception->getMessage(), 'Upload failed') !== false;
            });

        $mock = \Mockery::mock(FileStorageServiceProvider::class);
        $this->app->instance(FileStorageServiceProvider::class, $mock);

        $mock->shouldReceive('upload')
            ->andThrow(new \RuntimeException('Upload failed'));

        $file = $this->createTempXlsx(RowsImport::HEADERS);
        $response = $this->postJson('/upload', ['file' => $file]);

        $response->assertStatus(422)
            ->assertJsonFragment(['error' => 'Upload failed']);
    }

    public function test_it_returns_error_if_wrong_headers(): void
    {
        $mock = \Mockery::mock(FileStorageServiceProvider::class);
        $this->app->instance(FileStorageServiceProvider::class, $mock);

        $mock->shouldReceive('upload')
            ->andThrow(new \RuntimeException('Upload failed'));

        $file = $this->createTempXlsx(['id', 'name']);
        $response = $this->postJson('/upload', ['file' => $file]);

        $response->assertStatus(422)
            ->assertJsonFragment(['errors' => ['file' => ["The file has unexpected headers."]]]);
    }

}
