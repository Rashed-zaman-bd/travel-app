<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisaAssistanceRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'visa_service_id' => $this->visa_service_id,
            'service_name'    => $this->service_name,
            'name'            => $this->name,
            'phone'           => $this->phone,
            'email'           => $this->email,
            'message'         => $this->message,
            'status'          => $this->status,

            // Always the full {en, bn} titles, so the admin can pick a language
            'service' => $this->whenLoaded('visaService', fn () => $this->visaService ? [
                'id'    => $this->visaService->id,
                'title' => $this->visaService->title,
            ] : null),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
