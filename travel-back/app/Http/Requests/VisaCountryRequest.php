<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VisaCountryRequest extends FormRequest
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
         $id = $this->route('visaCountry')?->id;

        return [
            'name'          => ['required', 'array'],
             'name.en' => [
                'required', 'string', 'max:255',
                Rule::unique('visa_countries', 'name->en')->ignore($id),
            ],
            'name.bn'       => ['nullable', 'string', 'max:255'],

            'description'    => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.bn' => ['nullable', 'string'],

            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active'     => ['sometimes', 'boolean'],
            'sort_order'    => ['sometimes', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

}
