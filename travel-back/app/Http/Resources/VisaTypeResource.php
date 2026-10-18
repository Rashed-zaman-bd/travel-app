<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisaTypeResource extends JsonResource
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
            'id'          => $this->id,
            'name'        => $t($this->name),
            'slug'        => $this->slug,
            'description' => $t($this->description),
            'is_active'   => $this->is_active,
            'sort_order'  => $this->sort_order,
            'services'    => VisaServiceResource::collection($this->whenLoaded('visaServices')),
            'services_count' => $this->whenCounted('visaServices'),
            'created_at'  => $this->created_at?->toISOString(),
            'updated_at'  => $this->updated_at?->toISOString(),
        ];
    }
}
