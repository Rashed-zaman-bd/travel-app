<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTopBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],

            'image' => [
                'required',
                'image',
                'mimetypes:image/jpeg,image/png,image/gif',
                'max:5120',
            ],

            'order' => ['nullable', 'integer', 'min:0'],

            'active' => ['required', 'boolean'],
        ];
    }
}