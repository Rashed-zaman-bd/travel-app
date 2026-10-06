<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfferShowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'array'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'title.bn' => ['nullable', 'string', 'max:255'],

            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.bn' => ['nullable', 'string'],

            'location' => ['nullable', 'array'],
            'location.en' => ['nullable', 'string', 'max:255'],
            'location.bn' => ['nullable', 'string', 'max:255'],

            'price' => ['nullable', 'numeric', 'min:0'],

            'payment_method' => ['nullable', 'array'],
            'payment_method.en' => ['nullable', 'string', 'max:255'],
            'payment_method.bn' => ['nullable', 'string', 'max:255'],

            'discount' => ['nullable', 'array'],
            'discount.en' => ['nullable', 'string', 'max:255'],
            'discount.bn' => ['nullable', 'string', 'max:255'],

            'tour_duration' => ['nullable', 'array'],
            'tour_duration.en' => ['nullable', 'string', 'max:255'],
            'tour_duration.bn' => ['nullable', 'string', 'max:255'],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'url' => ['nullable', 'string', 'max:500'],

            'order' => ['nullable', 'integer', 'min:0'],

            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
