<?php

namespace App\Imports;

use App\Models\Row;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RowsImport implements ToModel, WithHeadingRow
{
    const HEADERS = ['id', 'name', 'date'];

    public function model(array $row): Row
    {
        $data = [];

        foreach (self::HEADERS as $header) {
            $data[$header] = $row[$header] ?? null;
        }

        return new Row($data);
    }
}
