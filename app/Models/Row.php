<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property Carbon $date
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Row extends Model
{
    /** @use HasFactory<\Database\Factories\RowFactory> */
    use HasFactory;

    protected  $fillable = [
        'id',
        'name',
        'date',
    ];
    protected $casts = [
        'id' => 'integer',
        'date' => 'date:d.m.Y',
    ];
}
