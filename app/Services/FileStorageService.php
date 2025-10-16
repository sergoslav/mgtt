<?php

namespace App\Services;

use App\Enums\ImportFileStatus;
use App\Helpers\UploadHelper;
use App\Jobs\RowImportJob;
use App\Models\ImportFile;
use App\Services\Contracts\FileStorageServiceProvider;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileStorageService implements FileStorageServiceProvider
{
    public function upload(UploadedFile $file): ImportFile
    {
        $originalName = $file->getClientOriginalName();
        $storedName = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        $stored = Storage::putFileAs(UploadHelper::path(), $file, $storedName);
        if (!$stored) {
            throw new \Exception('Could not store file');
        }

        /** @var ImportFile $importFile */
        $importFile = ImportFile::query()->create([
            'original_name' => substr($originalName, 0, 100),
            'stored_name' => $storedName,
            'status' => ImportFileStatus::Pending,
        ]);
        RowImportJob::dispatch(importFileId:  $importFile->id);

        return $importFile;
    }
}
