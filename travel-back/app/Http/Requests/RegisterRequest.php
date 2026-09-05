<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;


class RegisterRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => [
                'nullable', 
                'string', 
                'regex:/^01[3-9]\d{8}$/', 
                'unique:users,phone'
            ],
            'password' => ['required', 'string', 'min:8'],
            'name' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],

        ];
    }

}
