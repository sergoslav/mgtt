<?php

namespace App\Models;

use App\Enums\UploadedFileImportStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $original_name
 * @property string $stored_name
 * @property UploadedFileImportStatus $import_status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UploadedFile extends Model
{
    /** @use HasFactory<\Database\Factories\UploadedFileFactory> */
    use HasFactory;

    protected $fillable = [
        'original_name',
        'stored_name',
        'import_status',
    ];

    protected $casts = [
        'import_status'  => UploadedFileImportStatus::class,
    ];
}
