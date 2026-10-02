<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TourPackageItineraryRequest extends FormRequest
{
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
            'tour_package_id' => [ 'required', 'integer', 'exists:tour_packages,id' ],

            'day_number' => [ 'required', 'integer', 'min:1' ],

            'highlights' => [ 'nullable', 'array' ],
            'highlights.en' => [ 'nullable', 'string', 'max:2000' ],
            'highlights.bn' => [ 'nullable', 'string', 'max:2000' ],

            'overnight' => [ 'nullable', 'array' ],
            'overnight.en' => [ 'nullable', 'string', 'max:255' ],
            'overnight.bn' => [ 'nullable', 'string', 'max:255' ],

            'description' => [ 'nullable',  'array' ],
            'description.en' => [  'nullable',  'string' ],
            'description.bn' => [  'nullable', 'string' ],

            'map_image' => [ 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048' ],

            'image_caption' => [ 'nullable', 'array' ],
            'image_caption.en' => [ 'nullable', 'string', 'max:255' ],
            'image_caption.bn' => [  'nullable', 'string', 'max:255' ],

            'order' => [ 'nullable', 'integer', 'min:0' ],

            'is_active' => [ 'sometimes', 'boolean' ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'tour_package_id.required' =>
                'Tour package is required.',

            'tour_package_id.exists' =>
                'The selected tour package does not exist.',

            'day_number.required' =>
                'Day number is required.',

            'day_number.min' =>
                'Day number must be at least 1.',

            'title.required' =>
                'Itinerary title is required.',

            'title.array' =>
                'Title must be an object.',

            'title.en.required' =>
                'English title is required.',

            'highlights.array' =>
                'Highlights must be an object.',

            'overnight.array' =>
                'Overnight must be an object.',

            'description.array' =>
                'Description must be an object.',

            'image_caption.array' =>
                'Image caption must be an object.',

            'order.integer' =>
                'Order must be a number.',

            'order.min' =>
                'Order must be at least 0.',

            'is_active.boolean' =>
                'Active status must be true or false.',
        ];
    }
}
