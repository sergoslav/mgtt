<?php

namespace App\Helpers;

class UploadHelper
{
    /**
     * Uploads path
     */
    const UPLOAD_PATH =  'uploads';

    /**
     * Get Path to uploads
     * @return string
     */
    public static function path(): string
    {
        return self::UPLOAD_PATH;
    }

    /**
     * Get full path to uploaded file
     * @param string $fileName
     * @return string
     */
    public static function fullPath(string $fileName): string
    {
        return self::UPLOAD_PATH . '/' . $fileName;
    }
}
