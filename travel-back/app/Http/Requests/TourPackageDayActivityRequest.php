<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TourPackageDayActivityRequest extends FormRequest
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
            'tour_package_itinerary_id' => [
                'required',
                'integer',
                'exists:tour_package_itinerarys,id',
            ],

            'tour_package_id' => [
                'required',
                'integer',
                'exists:tour_packages,id',
            ],

            'title' => [
                'required',
                'array',
            ],

            'title.en' => [
                'required',
                'string',
                'max:255',
            ],

            'title.bn' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'array',
            ],

            'description.en' => [
                'nullable',
                'string',
            ],

            'description.bn' => [
                'nullable',
                'string',
            ],

            'image' => [ 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048' ],

            'image_caption' => [
                'nullable',
                'array',
            ],

            'image_caption.en' => [
                'nullable',
                'string',
                'max:255',
            ],

            'image_caption.bn' => [
                'nullable',
                'string',
                'max:255',
            ],

            'order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'tour_package_itinerary_id.required' =>
                'Itinerary is required.',

            'tour_package_itinerary_id.exists' =>
                'The selected itinerary does not exist.',

            'tour_package_id.required' =>
                'Tour package is required.',

            'tour_package_id.exists' =>
                'The selected tour package does not exist.',

            'title.required' =>
                'Activity title is required.',

            'title.array' =>
                'Title must be an object.',

            'title.en.required' =>
                'English activity title is required.',

            'description.array' =>
                'Description must be an object.',

            'image.max' =>
                'Image path may not exceed 255 characters.',

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
