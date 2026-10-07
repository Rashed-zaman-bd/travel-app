<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class TourPackageReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'is_approved' => $this->is_approved,

            // Reviewer details (retrieved from loaded User relationship)
            'user' => [
                'id' => $this->user_id,
                'name' => $this->whenLoaded('user', fn() => $this->user->name),
                'avatar_url' => $this->whenLoaded('user', fn() => $this->user->avatar_url ?? null),
            ],

            // Location & Travel Details
            'traveler_location' => $this->traveler_location,
            'destination_visited' => $this->destination_visited,
            'travel_type' => $this->travel_type,
            'travel_date' => $this->travel_date?->format('Y-m-d'),

            // Custom Accessor for direct UI display ("Traveled to Italy as a couple in September, 2026")
            'formatted_travel_info' => $this->formatted_travel_info,

            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

}