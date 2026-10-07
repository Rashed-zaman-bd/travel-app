<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class TourPackageReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating'      => ['required', 'integer', 'min:1', 'max:5'],
            'comment'     => ['nullable', 'string', 'max:2000'],
            'travel_type' => ['nullable', 'in:solo,couple,family,friends,business'],
            'travel_date' => ['nullable', 'date_format:Y-m,M-Y'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required'         => 'Please select a rating.',
            'rating.min'              => 'The rating must be between 1 and 5.',
            'rating.max'              => 'The rating must be between 1 and 5.',
            'travel_type.in'          => 'The selected travel type is invalid.',
            'travel_date.date_format' => 'The travel date must be in YYYY-MM format.',
        ];
    }

}