<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HeroSlideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust authorization logic if needed
    }

   public function rules(): array
{
    return [
        'title'          => ['required', 'array'],
        'title.en'       => ['required', 'string', 'max:255'],
        'title.bn'       => ['nullable', 'string', 'max:255'],

        'description'    => ['required', 'array'],
        'description.en' => ['required', 'string'],
        'description.bn' => ['nullable', 'string'],

        'com_name'       => ['nullable', 'array'],
        'com_name.en'    => ['nullable', 'string', 'max:255'],
        'com_name.bn'    => ['nullable', 'string', 'max:255'],

        'cta_text'       => ['nullable', 'array'],
        'cta_text.en'    => ['nullable', 'string', 'max:255'],
        'cta_text.bn'    => ['nullable', 'string', 'max:255'],

        'photo_text'     => ['nullable', 'array'],
        'photo_text.en'  => ['nullable', 'string', 'max:255'],
        'photo_text.bn'  => ['nullable', 'string', 'max:255'],

        'location'       => ['nullable', 'array'],
        'location.en'    => ['nullable', 'string', 'max:255'],
        'location.bn'    => ['nullable', 'string', 'max:255'],

        'author_name'    => ['nullable', 'array'],
        'author_name.en' => ['nullable', 'string', 'max:255'],
        'author_name.bn' => ['nullable', 'string', 'max:255'],

        'photo_date'     => ['nullable', 'array'],
        'photo_date.en'  => ['nullable', 'string', 'max:255'],
        'photo_date.bn'  => ['nullable', 'string', 'max:255'],

        // Image is now a real upload, not a string path.
        // Required on create (POST), optional on update (PUT/PATCH via _method override).
        'image' => [
            $this->isMethod('post') ? 'required' : 'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:5120',
        ],

        'cta_url'        => ['nullable', 'string', 'max:2255'],
        'order'          => ['nullable', 'integer', 'min:0'],
        'is_active'      => ['nullable', 'boolean'],
    ];
}

    public function messages(): array
    {
        return [
            'title.en.required'       => 'The English title is required.',
            'description.en.required' => 'The English description is required.',
            'image.required'          => 'A background image URL or path is required.',
        ];
    }
}
