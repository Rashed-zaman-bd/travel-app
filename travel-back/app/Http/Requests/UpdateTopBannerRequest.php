<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTopBannerRequest extends FormRequest
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
            'title' => [ 'required', 'string', 'max:255' ],

            'image' => [ 'nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048' ],

            'order' => [ 'nullable', 'integer', 'min:0' ],

            'active' => [ 'required', 'boolean' ],
        ];
    }
}
