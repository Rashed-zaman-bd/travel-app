<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogoResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'text_logo' => $this->text_logo ? asset('storage/' . $this->text_logo) : null,
            'round_logo' => $this->round_logo ? asset('storage/' . $this->round_logo) : null,
        ];
    }
}
