<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DestinationResource extends JsonResource
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
            'id'        => $this->id,
            'slug'      => $this->slug,
            'title'     => $t($this->title),
            'sub_title' => $t($this->sub_title),

            'image'       => $this->fileUrl($this->image),
            'image_title' => $t($this->image_title),

            'destination' => [
                'name'      => $t($this->destination_name),
                'title'     => $t($this->destination_title),
                'sub_title' => $t($this->destination_sub_title),
                'hero'      => [
                    'image' => $this->fileUrl($this->destination_hero_image),
                    'title' => $t($this->destination_hero_title),
                    'btn'   => $t($this->destination_hero_btn),
                ],
            ],

            'tour' => [
                'slug'        => $this->tour_slug,
                'title'       => $t($this->tour_title),
                'description' => $t($this->tour_description),
                'map_image'   => $this->fileUrl($this->map_image),
            ],

            'order'      => $this->order,
            'is_active'  => $this->is_active,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    private function fileUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return str_starts_with($path, 'http')
            ? $path
            : Storage::disk('public')->url($path);
    }
}