<?php

namespace App\Services\Contracts;

interface ImportServiceProvider
{
    /**
     * Import rows from File to DataBase
     *
     * @param int $importFileId
     * @return void
     */
    public function import(int $importFileId): void;
}
