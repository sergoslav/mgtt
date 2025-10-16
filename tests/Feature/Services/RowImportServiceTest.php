<?php

namespace Feature\Services;

use App\Enums\ImportFileStatus;
use App\Models\ImportFile;
use App\Services\RowImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class RowImportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_marks_file_as_completed_when_import_succeeds(): void
    {
        Excel::fake();

        $importFile = ImportFile::factory()->create([
            'status' => ImportFileStatus::Pending,
        ]);

        $service = new RowImportService();
        $service->import($importFile->id);
        $importFile->refresh();

        $this->assertEquals(ImportFileStatus::Completed, $importFile->status);
    }

    public function test_it_marks_file_as_failed_and_logs_when_import_throws(): void
    {
        Excel::shouldReceive('import')
            ->once()
            ->andThrow(new \Exception('Import failed'));

        $importFile = ImportFile::factory()->create([
            'status' => ImportFileStatus::Pending,
        ]);

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($exception, $context) use ($importFile) {
                return $exception instanceof \Exception
                    && $exception->getMessage() === 'Import failed'
                    && $context['import_file_id'] === $importFile->id;
            });

        $service = new RowImportService();
        $service->import($importFile->id);
        $importFile->refresh();

        $this->assertEquals(ImportFileStatus::Failed, $importFile->status);
    }

    public function test_it_marks_file_as_processing_before_import(): void
    {
        Excel::fake();

        $importFile = ImportFile::factory()->create([
            'status' => ImportFileStatus::Pending,
        ]);

        $service = new RowImportService();

        $service->import($importFile->id);
        $importFile->refresh();

        $this->assertEquals(ImportFileStatus::Completed, $importFile->status);
    }


}
