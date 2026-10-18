<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisaServiceResource extends JsonResource
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
            'title'           => $t($this->title),
            'description'     => $t($this->description),
            'processing_time' => $t($this->processing_time),
            'currency'        => $t($this->currency),
            'base_fee'        => $this->base_fee !== null ? (float) $this->base_fee : null,
            'eligibility'     => $t($this->eligibility),
            'application_url' => $this->application_url,
            'is_active'       => $this->is_active,

            'country' => new VisaCountryResource($this->whenLoaded('visaCountry')),
            'type'    => new VisaTypeResource($this->whenLoaded('visaType')),

            'created_at'      => $this->created_at?->toISOString(),
            'updated_at'      => $this->updated_at?->toISOString(),
        ];
    }
}
