<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'country_name',
        'image',
        'order',
        'is_active',
    ];

    protected $casts = [
        'country_name' => 'array',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Automatically create slug when creating a category.
     */
    protected static function booted(): void
    {
        // Creating
        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = static::generateUniqueSlug(
                    $category->country_name['en']
                    ?? $category->country_name['bn']
                    ?? 'category'
                );
            }
        });

        // Updating
        static::updating(function (Category $category) {

            // If English country name changed
            if ($category->isDirty('country_name')) {

                $countryName = $category->country_name;

                $englishName = $countryName['en']
                    ?? $countryName['bn']
                    ?? 'category';

                $category->slug = static::generateUniqueSlug(
                    $englishName,
                    $category->id
                );
            }
        });
    }

    /**
     * Generate a unique slug.
     */
    protected static function generateUniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {
        $slug = Str::slug($name);

        if ($slug === '') {
            $slug = 'category';
        }

        $originalSlug = $slug;
        $counter = 1;

        while (
            static::where('slug', $slug)
                ->when(
                    $ignoreId,
                    fn ($query) => $query->where('id', '!=', $ignoreId)
                )
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }


    /** * Country has many tour packages. */
    
    public function tourPackages(): HasMany 
    { 
        return $this->hasMany(TourPackage::class); 
    }



    /**
     * Route model binding uses slug.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
