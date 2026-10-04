<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TourPackageInformationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'tour_package_id' => [ 'required', 'integer', 'exists:tour_packages,id' ],

            'title' => [ 'required', 'array' ],
            'title.en' => [ 'required', 'string', 'max:1000' ],
            'title.bn' => [ 'nullable', 'string', 'max:1000' ],

            'content' => [ 'required', 'array' ],
            'content.en' => [ 'required', 'string', 'max:10000' ],
            'content.bn' => [ 'nullable', 'string', 'max:10000' ],

            'order' => [ 'nullable', 'integer', 'min:0' ],

            'is_active' => [ 'nullable', 'boolean' ],
        ];
    }

    public function messages(): array
    {
        return [
            'tour_package_id.required' => 'Tour package is required.',
            'tour_package_id.exists' => 'Selected tour package does not exist.',

            'title.required' => 'Title is required.',
            'title.array' => 'Title must be an object.',

            'title.en.required' => 'English Title is required.',
            'title.en.max' => 'English Title may not exceed 1000 characters.',

            'title.bn.max' => 'Bangla title may not exceed 1000 characters.',

            'content.required' => 'content is required.',
            'content.array' => 'content must be an object.',

            'content.en.required' => 'English content is required.',
            'content.en.max' => 'English content may not exceed 10000 characters.',

            'content.bn.max' => 'Bangla title may not exceed 10000 characters.',

            'order.integer' => 'Order must be a number.',
            'order.min' => 'Order must be at least 0.',

            'is_active.boolean' => 'Active status must be true or false.',
        ];
    }
}
