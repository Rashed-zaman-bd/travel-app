<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeroSlideResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        // Helper closure to resolve locale string with English fallback
        $getLocalized = fn(?array $attribute) => $attribute[$locale] ?? $attribute['en'] ?? null;

        return [
            'id'          => $this->id,
            
            // Dynamic localized text for front-end rendering
            'title'       => $getLocalized($this->title),
            'description' => $getLocalized($this->description),
            'com_name'    => $getLocalized($this->com_name),
            'cta_text'    => $getLocalized($this->cta_text),
            'photo_text'  => $getLocalized($this->photo_text),
            'location'    => $getLocalized($this->location),
            'author_name' => $getLocalized($this->author_name),
            'photo_date'  => $getLocalized($this->photo_date),

            // Raw JSON translation objects (Useful when loading edit forms in Admin panel)
            'translations' => $this->when($request->routeIs('admin.*'), [
                'title'       => $this->title,
                'description' => $this->description,
                'com_name'    => $this->com_name,
                'cta_text'    => $this->cta_text,
                'photo_text'  => $this->photo_text,
                'location'    => $this->location,
                'author_name' => $this->author_name,
                'photo_date'  => $this->photo_date,
            ]),

            // Non-translatable metadata
            'image'       => $this->image ? asset('storage/' . $this->image) : null,
            'cta_url'     => $this->cta_url,
            'order'       => $this->order,
            'is_active'   => $this->is_active,
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
