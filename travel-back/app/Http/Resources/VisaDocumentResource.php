<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisaDocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $all      = $request->boolean('all_locales'); // admin edit form
        $locale   = app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');

        $t = function (?array $value) use ($all, $locale, $fallback) {
            if ($all) {
                return $value ?? [];
            }

            return filled($value[$locale] ?? null)
                ? $value[$locale]
                : ($value[$fallback] ?? null);
        };

        return [
            'id'                        => $this->id,
            'visa_country_id'           => $this->visa_country_id,
            'visa_type_id'              => $this->visa_type_id,
            'visa_document_category_id' => $this->visa_document_category_id,
            'document_name'             => $t($this->document_name),
            'description'               => $t($this->description),
            'is_active'                 => $this->is_active,
            'sort_order'                => $this->sort_order,

            // Full {en, bn} names so the admin table can pick a language
            'country'  => $this->whenLoaded('visaCountry', fn () => $this->visaCountry ? [
                'id' => $this->visaCountry->id, 'name' => $this->visaCountry->name,
            ] : null),
            'type'     => $this->whenLoaded('visaType', fn () => $this->visaType ? [
                'id' => $this->visaType->id, 'name' => $this->visaType->name,
            ] : null),
            'category' => $this->whenLoaded('category', fn () => $this->category ? [
                'id' => $this->category->id, 'title' => $this->category->title,
            ] : null),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
