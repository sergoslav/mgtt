<?php

namespace Feature\Services;

use App\Enums\ImportFileStatus;
use App\Imports\RowsImport;
use App\Jobs\RowImportJob;
use App\Services\FileStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileStorageServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_file_and_creates_import_record(): void
    {
        Storage::fake('local');
        Queue::fake();

        $file = $this->createTempXlsx(RowsImport::HEADERS);

        $service = new FileStorageService();

        $importFile = $service->upload($file);

        Storage::disk('local')->assertExists($importFile->fullPath());

        $this->assertDatabaseHas('import_files', [
            'id' => $importFile->id,
            'original_name' => $file->getClientOriginalName(),
            'status' => ImportFileStatus::Pending,
        ]);

        // Job added to queue
        Queue::assertPushed(RowImportJob::class, function ($job) use ($importFile) {
            return $job->importFileId === $importFile->id;
        });
    }

    public function test_it_throws_exception_if_storage_fails(): void
    {
        Storage::shouldReceive('putFileAs')->andReturnFalse();

        $file = $this->createTempXlsx(RowsImport::HEADERS);

        $service = new FileStorageService();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Could not store file');

        $service->upload($file);
    }


}
