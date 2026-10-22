<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisaFeeOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'visa_country_id' => ['required', 'integer', 'exists:visa_countries,id'],
            'visa_type_id'    => ['required', 'integer', 'exists:visa_types,id'],

            'name'            => ['required', 'array'],
            'name.en'         => ['required', 'string', 'max:255'],
            'name.bn'         => ['nullable', 'string', 'max:255'],

            'description'     => ['nullable', 'array'],
            'description.en'  => ['nullable', 'string'],
            'description.bn'  => ['nullable', 'string'],

            'fee'             => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'is_active'       => ['sometimes', 'boolean'],
        ];
    }
}