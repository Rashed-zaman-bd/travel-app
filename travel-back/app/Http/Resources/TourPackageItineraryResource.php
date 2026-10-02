<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TourPackageItineraryResource extends JsonResource
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

            'day_number' => (int) $this->day_number,

            'highlights' => $this->highlights,

            'overnight' => $this->overnight,

            'description' => $this->description,

            'map_image' => $this->fileUrl($this->map_image),

            'image_caption' => $this->image_caption,

            'order' => (int) $this->order,

            'is_active' => (bool) $this->is_active,

            'activities' => TourPackageDayActivityResource::collection(
                $this->whenLoaded('activities')
            ),

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
