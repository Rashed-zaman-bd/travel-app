<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TourPackageDayActivityResource extends JsonResource
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

            'tour_package_itinerary_id' =>$this->tour_package_itinerary_id,

            'itinerary' => $this->whenloaded(
                'day',
                fn () => new TourPackageItineraryResource( $this->day)
            ),

            'tour_package' => $this->whenLoaded(
                'tourPackage',
                fn () => new TourPackageResource($this->tourPackage)
            ),

            'title' => $this->title,

            'description' => $this->description,

            'image' => $this->fileUrl($this->image),

            'image_caption' => $this->image_caption,

            'order' => (int) $this->order,

            'is_active' => (bool) $this->is_active,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    
    }

    private function fileUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}
