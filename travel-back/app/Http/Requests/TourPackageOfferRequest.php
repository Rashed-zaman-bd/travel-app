<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TourPackageOfferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tour_package_id' => [ 'required', 'integer', 'exists:tour_packages,id' ],

            'offer_name' => [ 'required', 'array' ],
            'offer_name.en' => [ 'required', 'string', 'max:100' ],
            'offer_name.bn' => [ 'nullable', 'string', 'max:100' ],

            'valid_from' => ['nullable', 'date'],
            'valid_from.en' => [ 'nullable', 'string', 'max:100' ],
            'valid_from.bn' => [ 'nullable', 'string', 'max:100' ],

            'valid_till' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'valid_till.en' => [ 'nullable', 'string', 'max:100' ],
            'valid_till.bn' => [ 'nullable', 'string', 'max:100' ],

            'departs' => [ 'nullable', 'array' ],
            'departs.en' => [ 'nullable', 'string', 'max:100' ],
            'departs.bn' => [ 'nullable', 'string', 'max:100' ],

            'price'      => ['nullable', 'numeric', 'min:0'],
            'price.en' => [ 'nullable', 'string', 'max:100' ],
            'price.bn' => [ 'nullable', 'string', 'max:100' ],

            'price_label' => [ 'nullable', 'array' ],
            'price_label.en' => [ 'nullable', 'string', 'max:100' ],
            'price_label.bn' => [ 'nullable', 'string', 'max:100' ],

            'price_note' => [ 'nullable', 'array' ],
            'price_note.en' => [ 'nullable', 'string', 'max:100' ],
            'price_note.bn' => [ 'nullable', 'string', 'max:100' ],

            'order' => [ 'nullable', 'integer', 'min:0' ],

            'is_active' => [ 'nullable', 'boolean' ],
        ];
    }
}
