<?php

namespace App\Services;

use App\Imports\RowsImport;
use App\Models\ImportFile;
use App\Services\Contracts\ImportServiceProvider;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class RowImportService implements ImportServiceProvider
{
    public function import(int $importFileId): void
    {
        $importFile = ImportFile::query()->findOrFail($importFileId);
        $parsingUniqueKey = "parsing-unique-key-{$importFileId}";

        $importFile->toProcessing();
        try {
            Excel::import(new RowsImport($parsingUniqueKey), $importFile->fullPath());
            $importFile->toCompleted();
        } catch (\Throwable $th) {
            $importFile->toFail();
            Log::error($th, ['import_file_id'  => $importFileId]);
        }

    }
}
