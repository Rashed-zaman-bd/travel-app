<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VisaServiceRequest extends FormRequest
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
        $isCreate = $this->isMethod('post');
        $req      = $isCreate ? 'required' : 'sometimes';
        $service  = $this->route('visa_service');
        $serviceId = is_object($service) ? $service->id : $service;

        $countryId = $this->input('visa_country_id');
        $typeId    = $this->input('visa_type_id');

        return [
            'visa_country_id' => [$req, 'integer', 'exists:visa_countries,id'],
            'visa_type_id'    => [
                $req, 'integer', 'exists:visa_types,id',
                // one service per country + type pair
                Rule::unique('visa_services', 'visa_type_id')
                    ->where(fn ($q) => $q->where('visa_country_id', $countryId))
                    ->ignore($serviceId),
            ],
            
            'title'            => [$req, 'array'],
            'title.en'         => [$req, 'string', 'max:255'],
            'title.bn'         => ['nullable', 'string', 'max:255'],

            'description'      => ['nullable', 'array'],
            'description.en'   => ['nullable', 'string', 'max:5000'],
            'description.bn'   => ['nullable', 'string', 'max:5000'],

            'processing_time'    => ['nullable', 'array'],
            'processing_time.en' => ['nullable', 'string', 'max:255'],
            'processing_time.bn' => ['nullable', 'string', 'max:255'],

            'currency'         => ['nullable', 'array'],
            'currency.en'      => ['nullable', 'string', 'max:50'],
            'currency.bn'      => ['nullable', 'string', 'max:50'],

            'base_fee'         => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],

            'eligibility'      => ['nullable', 'array'],
            'eligibility.en'   => ['nullable', 'string', 'max:5000'],
            'eligibility.bn'   => ['nullable', 'string', 'max:5000'],

            'application_url'  => ['nullable', 'url', 'max:255'],
            'is_active'        => ['sometimes', 'boolean'],
        ];
    }

     public function attributes(): array
    {
        return [
            'visa_country_id' => 'country',
            'visa_type_id'    => 'visa type',
        ];
    }

    public function messages(): array
    {
        return [
            'visa_type_id.unique' => 'A service for this country and visa type already exists.',
        ];
    }
}
