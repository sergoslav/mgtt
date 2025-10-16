<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadRequest;
use App\Services\Contracts\FileStorageServiceProvider;
use Illuminate\Support\Facades\Log;

class UploaderController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UploadRequest $request, FileStorageServiceProvider $fileStorage): \Illuminate\Http\JsonResponse
    {
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        try {
            $importFile = $fileStorage->upload($file);

            return response()->json([
                'data' => [
                    'id' => $importFile->id,
                    'status' => $importFile->status,
                    'original_name' => $originalName,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error($e, [
                'file_name' => $originalName,
                'import_file_id' => isset($importFile) ? $importFile->id : ''
            ]);

            return response()->json([
                'error' => $e->getMessage(),
                'data' => [
                    'id' => isset($importFile) ? $importFile->id : '',
                    'file' => $originalName,
                ],
            ], 422);
        }
    }
}
