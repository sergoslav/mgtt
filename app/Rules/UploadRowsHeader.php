<?php

namespace App\Rules;

use App\Imports\RowsImport;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Maatwebsite\Excel\HeadingRowImport;

class UploadRowsHeader implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $headings = (new HeadingRowImport)->toCollection($value);
        $headers = $headings->first()?->first()->toArray();

        if (!$headers || array_slice($headers, 0, count(RowsImport::HEADERS)) !== RowsImport::HEADERS) {
            $fail(__('The :attribute has unexpected headers.'));
        }
    }
}
