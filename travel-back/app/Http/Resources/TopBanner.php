<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TopBannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image
                ? Storage::disk('public')->url($this->image)
                : null,
            'order' => $this->order,
            'active' => (bool) $this->active,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}