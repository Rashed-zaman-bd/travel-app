<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisaDocumentCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $all      = $request->boolean('all_locales'); // admin forms
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
            'id'              => $this->id,
            'visa_service_id' => $this->visa_service_id,
            'title'           => $t($this->title),
            'is_active'       => $this->is_active,
            'sort_order'      => $this->sort_order,

            'service' => $this->whenLoaded('visaService', fn () => $this->visaService ? [
                'id'    => $this->visaService->id,
                'title' => $this->visaService->title, // full {en, bn}
            ] : null),

            'documents' => $this->whenLoaded('documents', fn () =>
                $this->documents->map(fn ($d) => [
                    'id'            => $d->id,
                    'document_name' => $t($d->document_name),
                    'description'   => $t($d->description),
                    'is_active'     => $d->is_active,
                    'sort_order'    => $d->sort_order,
                ])
            ),
            'documents_count' => $this->whenCounted('documents'),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}