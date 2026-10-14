<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VisaType extends Model
{
    use HasFactory;

    protected $table = 'visa_types';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (VisaType $visaType) {
            $visaType->slug = $visaType->generateUniqueSlug($visaType->name);
        });

        static::updating(function (VisaType $visaType) {
            // Get original and current English names
            $originalName = $visaType->getOriginal('name');
            $currentName = $visaType->name;

            $originalEn = is_array($originalName) ? ($originalName['en'] ?? null) : $originalName;
            $currentEn = is_array($currentName) ? ($currentName['en'] ?? null) : $currentName;

            // Only update the slug if the English name specifically has changed
            if ($originalEn !== $currentEn) {
                $visaType->slug = $visaType->generateUniqueSlug($visaType->name, $visaType->id);
            }
        });
    }

    /**
     * Generate a unique slug based on the translatable name.
     */
    public function generateUniqueSlug($name, $id = null): string
    {
        // Extract the English name as the base for the slug, or fallback to the first available language value
        $baseString = is_array($name) ? ($name['en'] ?? reset($name)) : $name;
        
        $slug = Str::slug($baseString);
        $originalSlug = $slug;
        $count = 1;

        // Ensure the slug is unique in the database
        while (static::where('slug', $slug)->when($id, fn ($query) => $query->where('id', '!=', $id))->exists()) {
            $slug = "{$originalSlug}-" . $count++;
        }

        return $slug;
    }
}