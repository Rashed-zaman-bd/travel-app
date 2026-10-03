<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CategoryResource extends JsonResource
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

            'slug' => $this->slug,

            'country_name' => $this->country_name,

            'image' => $this->fileUrl($this->image),

            'order' => (int) $this->order,

            'is_active' => (bool) $this->is_active,

            'easy_visa_destination' => (bool) $this->easy_visa_destination,

            'popular_destination'  => (bool) $this->popular_destination,

            'honeymoon'  => (bool) $this->honeymoon,

            'domestic'  => (bool) $this->domestic,
            
            'featured'  => (bool) $this->featured,

            'worldwide'  => (bool) $this->worldwide,

            'created_at' => $this->created_at?->toISOString(),

            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Convert storage path to public URL.
     */
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
