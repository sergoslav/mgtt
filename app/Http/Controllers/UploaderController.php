<?php

namespace App\Http\Controllers;

use App\Enums\UploadedFileImportStatus;
use App\Http\Requests\UploadRequest;
use App\Models\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploaderController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UploadRequest $request): \Illuminate\Http\JsonResponse
    {
        $file = $request->file('file');

        try {
            $originalName = $file->getClientOriginalName();
            $storedName = Storage::put('uploads', $file);

            /** @var UploadedFile $upload */
            $upload = UploadedFile::query()->create([
                'original_name' => $originalName,
                'stored_name' => $storedName,
                'import_status' => UploadedFileImportStatus::Pending,
            ]);

            //TODO: call event to process uploaded file

            return response()->json([
                'data' => [
                    'id' => $upload->id,
                    'status' => $upload->import_status,
                    'original_name' => $originalName,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error($e, [
                'file_name' => $originalName??'',
                'stored_name' => $storedName??'',
                'upload_id' => isset($upload) ? $upload->id : ''
            ]);

            return response()->json([
                'error' => $e->getMessage(),
                'data' => [
                    'id' => isset($upload) ? $upload->id : '',
                    'file_name' => $originalName??'',
                ],
            ], 422);
        }
    }
}
