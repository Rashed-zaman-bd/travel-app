<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourPackageOfferResource extends JsonResource
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
            'id' => $this->id,

            'tour_package_id' => $this->tour_package_id,

            'tour_package' => $this->whenLoaded(
                'tourPackage',
                fn () => new TourPackageResource($this->tourPackage)
            ),

            'offer_name' => $this->offer_name,
            'valid_from' => $this->valid_from?->format('Y-m-d'),
            'valid_till' => $this->valid_till?->format('Y-m-d'),
            'departs'    => $this->departs,
            'price'      => $this->price,
            'price_label' => $this->price_label,
            'price_note' => $this->price_note,

            'hotels' => TourPackageOfferHotelResource::collection($this->hotels),

            'order' => (int) $this->order,

            'is_active' => (bool) $this->is_active,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}
