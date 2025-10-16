<?php

namespace App\Services\Contracts;

use App\Models\ImportFile;
use Illuminate\Http\UploadedFile;

interface FileStorageServiceProvider
{
    /**
     * @param UploadedFile $file
     * @return ImportFile
     */
    public function upload(UploadedFile $file): ImportFile;
}
