<?php

namespace App\Imports;

use App\Events\RowCreated;
use App\Models\Row;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
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

    protected int $lastId = 0;

    public function __construct(
        public string $progressKey
    ) {
        Cache::store('redis')->set($this->progressKey, 0);
    }

    /**
     * @param array<string, mixed> $row
     * @return Row
     */
    public function model(array $row): Row
    {
        #calculate value in case a formula uses
        $id = is_numeric($row['id'] ?? null) ? (int)$row['id'] : ++$this->lastId;
        $this->lastId = $id;

        $rowModel =  new Row([
            'id' => $id,
            'name' => $row['name'],
            'date' => strlen($row['date']) > 8 ? Carbon::createFromFormat("d.m.Y", $row['date']) : Carbon::createFromFormat("d.m.y", $row['date']),
        ]);

        #Store progress to Redis
        Cache::store('redis')->increment($this->progressKey);

        //call it here and run after commit. as soon as can't catch on mass update by other way ¯\_(ツ)_/¯
        RowCreated::dispatch($rowModel);

        return $rowModel;
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

    public function chunkSize(): int
    {
        return 1000;
    }
}
