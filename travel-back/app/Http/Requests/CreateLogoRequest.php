<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateLogoRequest extends FormRequest
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
            'title' => ['nullable', 'string', 'max:255'],

            'text_logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],

            'round_logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ];

        
    }

    public function messages(): array
    {
        return [
            'text_logo.image' => 'Text logo must be an image.',
            'text_logo.mimes' => 'Text logo must be jpg, jpeg, png.',
            'text_logo.max' => 'Text logo must not be larger than 2MB.',

            'round_logo.image' => 'Round logo must be an image.',
            'round_logo.mimes' => 'Round logo must be jpg, jpeg, png.',
            'round_logo.max' => 'Round logo must not be larger than 2MB.',
        ];
    }

}
