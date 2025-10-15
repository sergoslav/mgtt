<?php

namespace App\Helpers;

class UploadHelper
{
    const UPLOAD_PATH =  'uploads';

    public static function fullPath(string $fileName): string
    {
        return self::UPLOAD_PATH . '/' . $fileName;
    }
}
