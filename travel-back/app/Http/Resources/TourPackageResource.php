<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TourPackageResource extends JsonResource
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
            'id' => $this->id,
            
            'category_id'       => $this->category_id,
            'category' => $this->whenLoaded('category', fn () => [
                'id'           => $this->category->id,
                'slug'         => $this->category->slug,
                'country_name' => $this->category->country_name,
            ]),

            'hero_image'        => $this->fileUrl($this->hero_image),
            'hero_image_title'  =>$this->hero_image_title,
            'hero_image_btn'    =>$this->hero_image_btn,
            'header'            =>$this->header,
            'sub_header'        =>$this->sub_header,
            'package_name'      =>$this->package_name,
            'slug'              =>$this->slug,
            'package_image'     =>$this->fileUrl($this->package_image),
            'package_price'     =>$this->package_price,
            'package_duration'  =>$this->package_duration,
            'package_image_title'=>$this->package_image_title,
            'package_map_image' =>$this->fileUrl($this->package_map_image),
            'package_destination'=>$this->package_destination,
            'order'              =>(int) $this->order,
            'is_active'          =>(bool) $this->is_active,

        ];
    }

    /**
     * Convert storage path to public URL.
     */
    private function fileUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}
