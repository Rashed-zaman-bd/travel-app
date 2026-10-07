<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TourPackageReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'tour_package_id' => $this->tour_package_id,
            'rating'          => (int) $this->rating,
            'comment'         => $this->comment,
            'is_approved'     => (bool) $this->is_approved,

            // from users table
            'user' => [
                'id'         => $this->user_id,
                'name'       => $this->user?->name,
                'avatar_url' => $this->avatarUrl($this->user?->avatar),
            ],

            'traveler_location'   => $this->traveler_location,
            'destination_visited' => $this->destination_visited,
            'travel_type'         => $this->travel_type,
            'travel_date'         => $this->travel_date?->format('Y-m-d'),

            // "Traveled to Italy as a couple in September, 2026"
            'formatted_travel_info' => $this->formatted_travel_info,

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    private function avatarUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        return filter_var($path, FILTER_VALIDATE_URL)
            ? $path
            : Storage::disk('public')->url($path);
    }
}