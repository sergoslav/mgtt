<?php

namespace Feature\Http\Controllers;

use App\Imports\RowsImport;
use App\Models\ImportFile;
use App\Models\Row;
use App\Services\Contracts\FileStorageServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RowControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_rows_grouped_by_date()
    {
        // Создаём строки с разными датами
        Row::factory()->create([
            'id' => 1,
            'name' => 'Alice',
            'date' => '2025-10-14',
        ]);

        Row::factory()->create([
            'id' => 2,
            'name' => 'Bob',
            'date' => '2025-10-14',
        ]);

        Row::factory()->create([
            'id' => 3,
            'name' => 'Charlie',
            'date' => '2025-10-15',
        ]);

        $response = $this->getJson('/rows?from_date=2025-10-14&to_date=2025-10-15');

        $response->assertOk()
            ->assertJson([
                [
                    'date' => '2025-10-14',
                    'rows' => [
                        ['id' => 1, 'name' => 'Alice'],
                        ['id' => 2, 'name' => 'Bob'],
                    ],
                ],
                [
                    'date' => '2025-10-15',
                    'rows' => [
                        ['id' => 3, 'name' => 'Charlie'],
                    ],
                ],
            ]);
    }

    public function test_it_requires_valid_dates()
    {
        $response = $this->getJson('/rows?from_date=invalid&to_date=alsoinvalid');

        $response->assertStatus(422); // RowsRequest должен валидировать даты
    }

}
