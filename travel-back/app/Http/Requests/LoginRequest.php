<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login'    => ['required_without_all:email,phone', 'nullable', 'string'],
            'email'    => ['required_without_all:login,phone', 'nullable', 'string'],
            'phone'    => ['required_without_all:login,email', 'nullable', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Consolidate input into a single 'login' field before validation passes to controller
     */
    protected function prepareForValidation(): void
    {
        $loginValue = $this->input('login') ?? $this->input('email') ?? $this->input('phone');

        $this->merge([
            'login' => $loginValue,
        ]);
    }

    public function messages(): array
    {
        return [
            'login.required'    => 'ইমেইল অথবা ফোন নম্বর আবশ্যক।',
            'password.required' => 'পাসওয়ার্ড আবশ্যক।',
        ];
    }
}