<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisaFeeOptionResource extends JsonResource
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
            'id'              => $this->id,
            'visa_country_id' => $this->visa_country_id,
            'visa_type_id'    => $this->visa_type_id,
            'name'            => $this->name,
            'description'     => $this->description,
            'fee'             => $this->fee,
            'is_active'       => $this->is_active,

            // Only included if the relation was eager loaded
            'visa_country'    => $this->whenLoaded('visaCountry'),
            'visa_type'       => $this->whenLoaded('visaType'),

            'created_at'      => $this->created_at?->toISOString(),
            'updated_at'      => $this->updated_at?->toISOString(),
        ];   
    }
}
