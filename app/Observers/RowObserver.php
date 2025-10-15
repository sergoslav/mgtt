<?php

namespace App\Observers;

use App\Events\RowCreated;
use App\Events\RowUpdated;
use App\Models\Row;

class RowObserver
{
    /**
     * Handle the Row "created" event.
     */
    public function created(Row $row): void
    {
        RowCreated::dispatch($row);
    }

    /**
     * Handle the Row "updated" event.
     */
    public function updated(Row $row): void
    {
        RowUpdated::dispatch($row);
    }

    /**
     * Handle the Row "deleted" event.
     */
    public function deleted(Row $row): void
    {
        //
    }

    /**
     * Handle the Row "restored" event.
     */
    public function restored(Row $row): void
    {
        //
    }

    /**
     * Handle the Row "force deleted" event.
     */
    public function forceDeleted(Row $row): void
    {
        //
    }
}
