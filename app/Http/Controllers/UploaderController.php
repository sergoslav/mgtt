<?php

namespace App\Http\Controllers;

use App\Enums\UploadedFileImportStatus;
use App\Helpers\UploadHelper;
use App\Http\Requests\UploadRequest;
use App\Jobs\ImportJob;
use App\Models\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            $storedName = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
            $stored = Storage::putFileAs(UploadHelper::UPLOAD_PATH, $file, $storedName);
            if (!$stored) {
                throw new \Exception('Could not store file');
            }

            /** @var UploadedFile $upload */
            $upload = UploadedFile::query()->create([
                'original_name' => substr($originalName, 0, 100),
                'stored_name' => $storedName,
                'import_status' => UploadedFileImportStatus::Pending,
            ]);
            ImportJob::dispatch(uploadedFileId:  $upload->id);

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
