<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NavItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'integer', 'exists:nav_items,id'],

            'title' => ['required', 'array'],
            'title.en' => ['required', 'string', 'max:255'],
            'title.bn' => ['required', 'string', 'max:255'],

            'url' => ['nullable', 'string', 'max:500'],

            'icon' => ['nullable', 'string', 'max:255'],

            'order' => ['nullable', 'integer', 'min:0'],

            'is_active' => ['nullable', 'boolean'],

            'open_new_tab' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'parent_id.exists' => 'The selected parent menu does not exist.',
            'title.required' => 'The menu title is required.',
            'title.array' => 'The menu title must include translations.',
            'title.en.required' => 'The English title is required.',
            'title.en.max' => 'The English title may not be greater than 255 characters.',
            'title.bn.required' => 'The Bengali title is required.',
            'title.bn.max' => 'The Bengali title may not be greater than 255 characters.',
            'order.integer' => 'The order must be a number.',
            'is_active.boolean' => 'The active status must be true or false.',
            'open_new_tab.boolean' => 'The open new tab value must be true or false.',
        ];
    }
}