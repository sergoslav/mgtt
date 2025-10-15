<?php

namespace App\Services\Contracts;

interface ImportServiceProvider
{
    /**
     * Import rows from File to DataBase
     *
     * @param int $uploadedFileId
     * @return void
     */
    public function import(int $uploadedFileId): void;
}
