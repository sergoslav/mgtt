<?php

namespace App\Models;

use App\Enums\ImportFileStatus;
use App\Helpers\UploadHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $original_name
 * @property string $stored_name
 * @property ImportFileStatus $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ImportFile extends Model
{
    /** @use HasFactory<\Database\Factories\ImportFileFactory> */
    use HasFactory;

    protected $fillable = [
        'original_name',
        'stored_name',
        'status',
    ];

    protected $casts = [
        'status'  => ImportFileStatus::class,
    ];

    /**
     * Put to Processing status
     * @return void
     */
    public function toProcessing(): void
    {
        $this->update(['status' => ImportFileStatus::Processing]);
    }

    /**
     * Put to Completed status
     * @return void
     */
    public function toCompleted(): void
    {
        $this->update(['status' => ImportFileStatus::Completed]);
        //TODO: to delete file?
    }

    /**
     * Put to Failed status
     * @return void
     */
    public function toFail(): void
    {
        $this->update(['status' => ImportFileStatus::Failed]);
    }

    public function fullPath(): string
    {
        return UploadHelper::fullPath($this->stored_name);
    }

}
