<?php

namespace App\Rules;

use App\Imports\RowsImport;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Maatwebsite\Excel\HeadingRowImport;

class UploadRowsHeader implements ValidationRule
{
    /**
     * @param string $attribute
     * @param \Illuminate\Http\UploadedFile $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $headings = (new HeadingRowImport)->toCollection($value);
            $headers = $headings->first()?->first()->toArray();

            if (!$headers || array_slice($headers, 0, count(RowsImport::HEADERS)) !== RowsImport::HEADERS) {
                $fail(__('The :attribute has unexpected headers.'));
            }
        } catch (\Throwable $e) {
            $fail(__('The :attribute has unexpected content.'));
        }
    }
}
