<?php

namespace App\Jobs;

use App\Services\Contracts\ImportServiceProvider;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RowImportJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $importFileId,
    )
    {
        $this->onQueue('import');
    }

    /**
     * Execute the job.
     */
    public function handle(ImportServiceProvider $service): void
    {
        $service->import(importFileId: $this->importFileId);
    }
}
