<?php

namespace App\Services;

use App\Enums\UploadedFileImportStatus;
use App\Helpers\UploadHelper;
use App\Imports\RowsImport;
use App\Models\UploadedFile;
use App\Services\Contracts\ImportServiceProvider;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImportServiceService implements ImportServiceProvider
{
    public function import(int $uploadedFileId): void
    {
        $uploadedFile = UploadedFile::query()->findOrFail($uploadedFileId);

        $uploadedFile->update(['import_status' => UploadedFileImportStatus::Processing]);

        try {
            Excel::import(new RowsImport, UploadHelper::fullPath($uploadedFile->stored_name));
            $uploadedFile->update(['import_status' => UploadedFileImportStatus::Completed]);
        } catch (\Throwable $th) {
            Log::error($th, ['upload_id'  => $uploadedFileId]);
            $uploadedFile->update(['import_status' => UploadedFileImportStatus::Failed]);
        }

    }
}
