<?php

namespace App\Imports;

use App\Models\Row;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\RemembersChunkOffset;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class RowsImport implements ToModel, WithHeadingRow, WithUpserts, WithBatchInserts, WithChunkReading, SkipsEmptyRows
{
    const HEADERS = ['id', 'name', 'date'];

    use RemembersRowNumber;
    use RemembersChunkOffset;

    protected static int $lastId = 0;

    public function __construct(
        public ?string $progressKey = null
    ) {
        if (is_null($this->progressKey)) {
            $this->progressKey = Str::uuid()->toString();
        }

        Cache::store('redis')->set($this->progressKey, 0); // инициализируем
    }

    public function model(array $row): Row
    {
        #Store progress to Redis
        $currentRowNumber = $this->getRowNumber();
        $currentRowNumber--; //Do we need exclude header in this calc?
        Cache::store('redis')->set($this->progressKey, $currentRowNumber); //Is it critical to update it after row inserter to DB?

        #calculate value in case a formula uses
        $id = is_numeric($row['id'] ?? null) ? (int)$row['id'] : null;
        if ($id === null) {
            $id = ++self::$lastId;
        } else {
            self::$lastId = $id;
        }

        return new Row([
            'id' => $id,
            'name' => $row['name'] ?? null,
            'date' => $row['date'] ?? null,
        ]);
    }

    public function uniqueBy(): string
    {
        //Do we need update or insert ?
        return 'id';
    }

    public function batchSize(): int
    {
        return 1000;
    }
//
    public function chunkSize(): int
    {
        return 1000;
    }
}
