<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TourPackageReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tour_package_id'     => ['required', 'integer', 'exists:tour_packages,id'],
            'rating'              => ['required', 'integer', 'min:1', 'max:5'],
            'comment'             => ['nullable', 'string', 'max:2000'],
            'traveler_location'   => ['nullable', 'string', 'max:255'],
            'destination_visited' => ['nullable', 'string', 'max:255'],
            'travel_type'         => ['nullable', 'string', Rule::in(['solo', 'couple', 'family', 'group', 'business'])],
            'travel_date'         => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tour_package_id'     => 'tour package',
            'traveler_location'   => 'location',
            'destination_visited' => 'destination',
            'travel_type'         => 'travel type',
            'travel_date'         => 'travel date',
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required'          => 'Please select a rating.',
            'tour_package_id.required' => 'The tour package is missing.',
            'tour_package_id.exists'   => 'The selected tour package does not exist.',
            'travel_date.before_or_equal' => 'The travel date cannot be in the future.',
        ];
    }
}