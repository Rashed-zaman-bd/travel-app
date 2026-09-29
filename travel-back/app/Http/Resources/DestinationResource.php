<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DestinationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'slug'     => $this->slug,
            'title'    => $this->title,
            'sub_title' => $this->sub_title,

            'image'       => $this->fileUrl($this->image),
            'image_title' => $this->image_title,

            'destination' => [
                'name'      => $this->destination_name,
                'title'     => $this->destination_title,
                'sub_title' => $this->destination_sub_title,
                'hero'      => [
                    'image' => $this->fileUrl($this->destination_hero_image),
                    'title' => $this->destination_hero_title,
                    'btn'   => $this->destination_hero_btn,
                ],
            ],

            'tour' => [
                'slug'        => $this->tour_slug,
                'title'       => $this->tour_title,
                'description' => $this->tour_description,
                'map_image'   => $this->fileUrl($this->map_image),
            ],

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