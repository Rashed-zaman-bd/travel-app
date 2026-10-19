<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisaDocumentCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $req = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'visa_service_id' => [$req, 'integer', 'exists:visa_services,id'],

            'title'    => [$req, 'array'],
            'title.en' => [$req, 'string', 'max:255'],
            'title.bn' => ['nullable', 'string', 'max:255'],

            'is_active'  => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'visa_service_id' => 'service',
            'title.en'        => 'title (EN)',
            'title.bn'        => 'title (BN)',
        ];
    }
}
