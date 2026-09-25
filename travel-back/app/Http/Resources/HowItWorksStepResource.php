<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HowItWorksStepResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $getLocalized = fn(?array $attribute) => $attribute[$locale] ?? $attribute['en'] ?? null;

        $iconParts = explode(':', $this->icon, 2);

        return [
            'id'          => $this->id,
            'heading'     => $getLocalized($this->heading),
            'topline'     => $getLocalized($this->topline),
            'title'       => $getLocalized($this->title),
            'description' => $getLocalized($this->description),

            'translations' => $this->when($request->routeIs('admin.*'), [
                'heading' => $this->heading,
                'topline'     => $this->topline,
                'title'       => $this->title,
                'description' => $this->description,
            ]),

            'icon'        => count($iconParts) === 2 ? $iconParts : ['fas', $this->icon],
            'order'       => $this->order,
            'is_active'   => $this->is_active,
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}