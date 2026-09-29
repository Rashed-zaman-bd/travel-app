<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DestinationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $image = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];

        return [
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('destinations', 'slug')->ignore($this->route('destination')),
            ],

            'title'        => ['required', 'array'],
            'title.en'     => ['required', 'string', 'max:255'],
            'title.bn'     => ['nullable', 'string', 'max:255'],

            'sub_title'    => ['required', 'array'],
            'sub_title.en' => ['required', 'string'],
            'sub_title.bn' => ['nullable', 'string'],

            'image'        => $image,
            'image_title'    => ['nullable', 'array'],
            'image_title.en' => ['nullable', 'string', 'max:255'],
            'image_title.bn' => ['nullable', 'string', 'max:255'],

            'destination_name'    => ['required', 'array'],
            'destination_name.en' => ['required', 'string', 'max:255'],
            'destination_name.bn' => ['nullable', 'string', 'max:255'],

            'destination_title'    => ['nullable', 'array'],
            'destination_title.en' => ['nullable', 'string', 'max:255'],
            'destination_title.bn' => ['nullable', 'string', 'max:255'],

            'destination_sub_title'    => ['nullable', 'array'],
            'destination_sub_title.en' => ['nullable', 'string'],
            'destination_sub_title.bn' => ['nullable', 'string'],

            'destination_hero_image' => $image,
            'destination_hero_title'    => ['nullable', 'array'],
            'destination_hero_title.en' => ['nullable', 'string', 'max:255'],
            'destination_hero_title.bn' => ['nullable', 'string', 'max:255'],
            'destination_hero_btn'      => ['nullable', 'array'],
            'destination_hero_btn.en'   => ['nullable', 'string', 'max:255'],
            'destination_hero_btn.bn'   => ['nullable', 'string', 'max:255'],

            'tour_slug'  => ['nullable', 'string', 'max:255'],
            'tour_title'    => ['nullable', 'array'],
            'tour_title.en' => ['nullable', 'string', 'max:255'],
            'tour_title.bn' => ['nullable', 'string', 'max:255'],
            'tour_description'    => ['nullable', 'array'],
            'tour_description.en' => ['nullable', 'string'],
            'tour_description.bn' => ['nullable', 'string'],

            'map_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'order'           => ['nullable', 'integer', 'min:0'],
            'is_active'       => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.en.required'            => 'The English title is required.',
            'sub_title.en.required'        => 'The English sub title is required.',
            'destination_name.en.required' => 'The English destination name is required.',
            'slug.unique'                  => 'This slug already exists.',
        ];
    }
}