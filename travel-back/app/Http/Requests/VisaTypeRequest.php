<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisaTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isCreate = $this->isMethod('post');

        return [
            'name'           => [$isCreate ? 'required' : 'sometimes', 'array'],
            'name.en'        => [$isCreate ? 'required' : 'sometimes', 'string', 'max:255'],
            'name.bn'        => ['nullable', 'string', 'max:255'],

            'description'    => ['nullable', 'array'],
            'description.en' => ['nullable', 'string', 'max:1000'],
            'description.bn' => ['nullable', 'string', 'max:1000'],

            'is_active'      => ['sometimes', 'boolean'],
            'sort_order'     => ['sometimes', 'integer', 'min:0'],
        ];
    }
}