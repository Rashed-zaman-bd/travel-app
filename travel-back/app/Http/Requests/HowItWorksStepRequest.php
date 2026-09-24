<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HowItWorksStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'heading'    => ['required', 'array'],
            'heading.en' => ['required', 'string', 'max:100'],
            'heading.bn' => ['nullable', 'string', 'max:100'],

            'topline'         => ['required', 'array'],
            'topline.en'      => ['required', 'string', 'max:50'],
            'topline.bn'      => ['nullable', 'string', 'max:50'],

            'title'           => ['required', 'array'],
            'title.en'        => ['required', 'string', 'max:255'],
            'title.bn'        => ['nullable', 'string', 'max:255'],

            'description'     => ['required', 'array'],
            'description.en'  => ['required', 'string'],
            'description.bn'  => ['nullable', 'string'],

            'icon'            => ['required', 'string', 'max:100'],
            'order'           => ['nullable', 'integer', 'min:0'],
            'is_active'       => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'topline.en.required'     => 'The English topline (e.g. "STEP") is required.',
            'title.en.required'       => 'The English title is required.',
            'description.en.required' => 'The English description is required.',
            'icon.required'           => 'An icon is required (e.g. fas:comments).',
        ];
    }
}
