<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DestinationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            // Main destination information
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string', 'max:255'],
            'title.bn' => ['nullable', 'string', 'max:255'],

            'sub_title' => ['required', 'array'],
            'sub_title.en' => ['required', 'string'],
            'sub_title.bn' => ['nullable', 'string'],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('destinations', 'slug')->ignore($this->destination),
            ],

            // Main image
            'image' => ['nullable', 'string', 'max:255'],

            'image_title' => ['nullable', 'array'],
            'image_title.en' => ['nullable', 'string', 'max:255'],
            'image_title.bn' => ['nullable', 'string', 'max:255'],

            // Destination information
            'destination_name' => ['required', 'array'],
            'destination_name.en' => ['required', 'string', 'max:255'],
            'destination_name.bn' => ['nullable', 'string', 'max:255'],

            'destination_title' => ['nullable', 'array'],
            'destination_title.en' => ['nullable', 'string', 'max:255'],
            'destination_title.bn' => ['nullable', 'string', 'max:255'],

            'destination_sub_title' => ['nullable', 'array'],
            'destination_sub_title.en' => ['nullable', 'string'],
            'destination_sub_title.bn' => ['nullable', 'string'],

            // Hero section
            'destination_hero_image' => ['nullable', 'string', 'max:255'],

            'destination_hero_title' => ['nullable', 'array'],
            'destination_hero_title.en' => ['nullable', 'string', 'max:255'],
            'destination_hero_title.bn' => ['nullable', 'string', 'max:255'],

            'destination_hero_btn' => ['nullable', 'array'],
            'destination_hero_btn.en' => ['nullable', 'string', 'max:255'],
            'destination_hero_btn.bn' => ['nullable', 'string', 'max:255'],

            // Tour information
            'tour_slug' => ['nullable', 'string', 'max:255'],

            'tour_title' => ['nullable', 'array'],
            'tour_title.en' => ['nullable', 'string', 'max:255'],
            'tour_title.bn' => ['nullable', 'string', 'max:255'],

            'tour_description' => ['nullable', 'array'],
            'tour_description.en' => ['nullable', 'string'],
            'tour_description.bn' => ['nullable', 'string'],

            // Map
            'map_image' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The title is required.',
            'title.en.required' => 'The English title is required.',

            'sub_title.required' => 'The sub title is required.',
            'sub_title.en.required' => 'The English sub title is required.',

            'slug.required' => 'The slug is required.',
            'slug.unique' => 'This slug already exists.',

            'destination_name.required' => 'The destination name is required.',
            'destination_name.en.required' => 'The English destination name is required.',
        ];
    }
}
