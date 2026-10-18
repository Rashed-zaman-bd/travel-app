<?php

namespace App\Http\Resources;

use App\Http\Resources\VisaServiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class VisaCountryResource extends JsonResource
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
        'name'        => $t($this->name),          // was $this->name
        'slug'        => $this->slug,
        'image'       => $this->fileUrl($this->image),
        'description' => $t($this->description),   // was $this->description
        'is_active'   => $this->is_active,
        'sort_order'  => $this->sort_order,
        'services'    => VisaServiceResource::collection($this->whenLoaded('visaServices')), // was visaService
        'created_at'  => $this->created_at?->toISOString(),
        'updated_at'  => $this->updated_at?->toISOString(),
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
