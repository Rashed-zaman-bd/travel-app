<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TourPackageOfferHotelRequest extends FormRequest
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

            'tour_package_offer_id' => [
                'required', 'integer',
                Rule::exists('tour_package_offers', 'id')
                    ->where('tour_package_id', $this->input('tour_package_id')),
            ],

            'hotel_name' => [ 'required', 'array' ],
            'hotel_name.en' => [ 'required', 'string', 'max:100' ],
            'hotel_name.bn' => [ 'nullable', 'string', 'max:100' ],

            'location' => [ 'nullable', 'array' ],
            'location.en' => [ 'nullable', 'string', 'max:100' ],
            'location.bn' => [ 'nullable', 'string', 'max:100' ],

            'order' => [ 'nullable', 'integer', 'min:0' ],

            'is_active' => [ 'nullable', 'boolean' ],
        ];
    }
}
