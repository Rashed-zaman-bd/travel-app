<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VisaAssistanceRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Public form (POST): visitors can't set the status
        if ($this->isMethod('post')) {
            return [
                'visa_service_id' => ['nullable', 'integer', 'exists:visa_services,id'],
                'name'            => ['required', 'string', 'max:255'],
                'phone'           => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{7,30}$/'],
                'email'           => ['nullable', 'email', 'max:255'],
                'message'         => ['nullable', 'string', 'max:2000'],
            ];
        }

        // Admin update (PUT/PATCH)
        return [
            'name'    => ['sometimes', 'string', 'max:255'],
            'phone'   => ['sometimes', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{7,30}$/'],
            'email'   => ['nullable', 'email', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
            'status'  => ['sometimes', Rule::in(['pending', 'contacted', 'completed', 'cancelled'])],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid phone number.',
        ];
    }
}
