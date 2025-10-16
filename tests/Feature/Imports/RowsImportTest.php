<?php

namespace Feature\Imports;

use App\Events\RowCreated;
use App\Imports\RowsImport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RowsImportTest extends TestCase
{

    public function test_it_creates_row_with_long_date(): void
    {
        Event::fake();

        $import = new RowsImport('progress-key');

        $rowData = [
            'id' => 123,
            'name' => 'John Doe',
            'date' => '16.10.2025',
        ];
        $rowModel = $import->model($rowData);

        $this->assertEquals(123, $rowModel->id);
        $this->assertEquals('John Doe', $rowModel->name);
        $this->assertEquals(Carbon::createFromFormat('d.m.Y', '16.10.2025'), $rowModel->date);

        // check event
        Event::assertDispatched(RowCreated::class, function ($event) use ($rowModel) {
            return $event->row->id === $rowModel->id;
        });
    }

    public function test_it_creates_row_with_short_date(): void
    {
        Event::fake();
        $import = new RowsImport('progress-key');

        $rowData = [
            'id' => 1234,
            'name' => 'John Doe',
            'date' => '16.10.25',
        ];
        $rowModel = $import->model($rowData);

        $this->assertEquals(1234, $rowModel->id);
        $this->assertEquals('John Doe', $rowModel->name);
        $this->assertEquals(Carbon::createFromFormat('d.m.Y', '16.10.2025'), $rowModel->date);
    }

    public function test_it_generates_id_when_id_is_formula(): void
    {
        Event::fake();
        $import = new RowsImport('progress-key');

        $rowData = [
            'id' => 1,
            'name' => 'Alice',
            'date' => '16.10.25', // короткая дата
        ];
        $rowModel = $import->model($rowData);

        $this->assertEquals(1, $rowModel->id);
        $this->assertEquals('Alice', $rowModel->name);
        $this->assertEquals(Carbon::createFromFormat('d.m.Y', '16.10.2025'), $rowModel->date);

        //Add second line with formula in `id` field
        $rowData = [
            'id' => "=A1+1",
            'name' => 'Bob',
            'date' => '17.10.25', // короткая дата
        ];
        $rowModel = $import->model($rowData);

        $this->assertEquals(2, $rowModel->id);
        $this->assertEquals('Bob', $rowModel->name);
        $this->assertEquals(Carbon::createFromFormat('d.m.Y', '17.10.2025'), $rowModel->date);

        //Add new line with number in `id` field
        $rowData = [
            'id' => 5,
            'name' => 'Bob',
            'date' => '17.10.25', // короткая дата
        ];
        $rowModel = $import->model($rowData);

        $this->assertEquals(5, $rowModel->id);
        $this->assertEquals('Bob', $rowModel->name);
        $this->assertEquals(Carbon::createFromFormat('d.m.Y', '17.10.2025'), $rowModel->date);
    }

    public function test_it_creates_row_and_update_number_in_redis(): void
    {
        Event::fake();
        Cache::store('redis')->flush();

        $parsingKey = 'progress-key-1';

        $import = new RowsImport($parsingKey);

        $rowData = ['id' => 1, 'name' => 'John Doe', 'date' => '16.10.2025',];
        $rowModel = $import->model($rowData);

        $this->assertEquals(1, $rowModel->id);
        // check progressKey in Cache
        $this->assertEquals(1, Cache::store('redis')->get($parsingKey));

        //Add second row
        $rowData = ['id' => 44, 'name' => 'John Doe', 'date' => '16.10.2025',];
        $rowModel = $import->model($rowData);

        $this->assertEquals(44, $rowModel->id);
        // check progressKey in Cache
        $this->assertEquals(2, Cache::store('redis')->get($parsingKey));
    }
}
