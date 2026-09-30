<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $image = [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:2048',
        ];

        return [
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')
                    ->ignore($this->route('category')),
            ],

            'country_name' => [
                'required',
                'array',
            ],

            'country_name.en' => [
                'required',
                'string',
                'max:255',
            ],

            'country_name.bn' => [
                'nullable',
                'string',
                'max:255',
            ],

            'image' => $image,

            'order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'country_name.required' =>
                'The country name is required.',

            'country_name.en.required' =>
                'The English country name is required.',

            'slug.unique' =>
                'This slug already exists.',
        ];
    }
}