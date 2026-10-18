<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
            
            $originalName = $visaType->getOriginal('name');
            $currentName = $visaType->name;

            $originalEn = is_array($originalName) ? ($originalName['en'] ?? null) : $originalName;
            $currentEn = is_array($currentName) ? ($currentName['en'] ?? null) : $currentName;

           
            if ($originalEn !== $currentEn) {
                $visaType->slug = $visaType->generateUniqueSlug($visaType->name, $visaType->id);
            }
        });
    }

    public function generateUniqueSlug($name, $id = null): string
    {
     
        $baseString = is_array($name) ? ($name['en'] ?? reset($name)) : $name;
        
        $slug = Str::slug($baseString);
        $originalSlug = $slug;
        $count = 1;

       
        while (static::where('slug', $slug)->when($id, fn ($query) => $query->where('id', '!=', $id))->exists()) {
            $slug = "{$originalSlug}-" . $count++;
        }

        return $slug;
    }


    public function visaServices(): HasMany
    {
        return $this->hasMany(VisaService::class, 'visa_type_id');
    }
}