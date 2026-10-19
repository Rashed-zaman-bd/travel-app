<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisaDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $req = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'visa_country_id'           => [$req, 'integer', 'exists:visa_countries,id'],
            'visa_type_id'              => [$req, 'integer', 'exists:visa_types,id'],
            'visa_document_category_id' => [$req, 'integer', 'exists:visa_document_categories,id'],

            'document_name'    => [$req, 'array'],
            'document_name.en' => [$req, 'string', 'max:1000'],
            'document_name.bn' => ['nullable', 'string', 'max:1000'],

            'description'      => ['nullable', 'array'],
            'description.en'   => ['nullable', 'string', 'max:2000'],
            'description.bn'   => ['nullable', 'string', 'max:2000'],

            'is_active'        => ['sometimes', 'boolean'],
            'sort_order'       => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'visa_country_id'           => 'country',
            'visa_type_id'              => 'visa type',
            'visa_document_category_id' => 'category',
            'document_name.en'          => 'document (EN)',
            'document_name.bn'          => 'document (BN)',
        ];
    }
}
