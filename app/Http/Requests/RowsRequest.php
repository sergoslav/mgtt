<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RowsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from_date' => ['required', 'date', 'before_or_equal:to_date'],
            'to_date'   => ['required', 'date', 'after_or_equal:from_date'],
        ];
    }
}
