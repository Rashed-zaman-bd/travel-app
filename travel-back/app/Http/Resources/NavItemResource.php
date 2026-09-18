<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NavItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'title' => $this->title,
            'url' => $this->url,
            'icon' => $this->icon,
            'order' => $this->order,
            'is_active' => (bool) $this->is_active,
            'open_new_tab' => (bool) $this->open_new_tab,

            // Automatically maps recursively if loaded
            'children' => NavItemResource::collection(
                $this->whenLoaded('childrenRecursive', fn() => $this->childrenRecursive, fn() => $this->whenLoaded('children'))
            ),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}