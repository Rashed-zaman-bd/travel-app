<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TourPackageHighlightRequest extends FormRequest
{
      public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tour_package_id' => [ 'required', 'integer', 'exists:tour_packages,id' ],

            'highlight' => [ 'required', 'array' ],
            'highlight.en' => [ 'required', 'string', 'max:1000' ],
            'highlight.bn' => [ 'nullable', 'string', 'max:1000' ],

            'icon' => [ 'nullable', 'string', 'max:255' ],

            'order' => [ 'nullable', 'integer', 'min:0' ],

            'is_active' => [ 'sometimes', 'boolean' ],
        ];
    }

    public function messages(): array
    {
        return [
            'tour_package_id.required' => 'Tour package is required.',
            'tour_package_id.exists' => 'Selected tour package does not exist.',

            'highlight.required' => 'Highlight is required.',
            'highlight.array' => 'Highlight must be an object.',

            'highlight.en.required' => 'English highlight is required.',
            'highlight.en.max' => 'English highlight may not exceed 1000 characters.',

            'highlight.bn.max' => 'Bangla highlight may not exceed 1000 characters.',

            'icon.max' => 'Icon may not exceed 255 characters.',

            'order.integer' => 'Order must be a number.',
            'order.min' => 'Order must be at least 0.',

            'is_active.boolean' => 'Active status must be true or false.',
        ];
    }
}
