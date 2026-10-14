<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class VisaCountry extends Model
{
    use HasFactory;

    protected $table = 'visa_countries';

    protected $fillable = [
        'name',
        'slug',
        'image',
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
        static::creating(function (VisaCountry $visaCountry) {
            $visaCountry->slug = $visaCountry->generateUniqueSlug($visaCountry->name);
        });

        static::updating(function (VisaCountry $visaCountry) {
            // Get original and current English names
            $originalName = $visaCountry->getOriginal('name');
            $currentName = $visaCountry->name;

            $originalEn = is_array($originalName) ? ($originalName['en'] ?? null) : $originalName;
            $currentEn = is_array($currentName) ? ($currentName['en'] ?? null) : $currentName;

            // Only update the slug if the English name specifically has changed
            if ($originalEn !== $currentEn) {
                $visaCountry->slug = $visaCountry->generateUniqueSlug($visaCountry->name, $visaCountry->id);
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

    public function visaService(): HasMany
    {
        return $this->hasMany( VisaService::class, 'visa_service_id' );
    }
}