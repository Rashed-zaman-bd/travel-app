<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TourPackage extends Model
{
    use HasFactory;

    protected $table = 'tour_packages';

    protected $fillable = [
        'category_id',
        'hero_image',
        'hero_image_title',
        'hero_image_btn',
        'header',
        'sub_header',
        'package_name',
        'slug',
        'package_image',
        'package_price',
        'package_duration',
        'package_image_title',
        'package_map_image',
        'package_destination',
        'order',
        'is_active',
    ];

    protected $casts = [
        'hero_image_title' => 'array',
        'hero_image_btn' => 'array',
        'header' => 'array',
        'sub_header' => 'array',
        'package_name' => 'array',
        'package_price' => 'array',
        'package_duration' => 'array',
        'package_image_title' => 'array',
        'package_destination' => 'array',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Automatically create/update slug.
     */
    protected static function booted(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Creating
        |--------------------------------------------------------------------------
        */
        static::creating(function (TourPackage $tourPackage) {

            if (empty($tourPackage->slug)) {

                $packageName = $tourPackage->package_name ?? [];

                $name = $packageName['en']
                    ?? $packageName['bn']
                    ?? 'tour-package';

                $tourPackage->slug = static::generateUniqueSlug($name);
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Updating
        |--------------------------------------------------------------------------
        */
        static::updating(function (TourPackage $tourPackage) {

            // package_name changed
            if ($tourPackage->isDirty('package_name')) {

                $packageName = $tourPackage->package_name ?? [];

                $name = $packageName['en']
                    ?? $packageName['bn']
                    ?? 'tour-package';

                $tourPackage->slug = static::generateUniqueSlug(
                    $name,
                    $tourPackage->id
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

        // If name cannot generate a slug
        if ($slug === '') {
            $slug = 'tour-package';
        }

        $originalSlug = $slug;
        $counter = 1;

        while (
            static::where('slug', $slug)
                ->when(
                    $ignoreId !== null,
                    fn ($query) =>
                        $query->where('id', '!=', $ignoreId)
                )
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Tour package belongs to a country/category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

     public function highlights(): HasMany
    {
        return $this->hasMany(TourPackageHighlight::class)
            ->orderBy('order');
    }

    public function days(): HasMany
    {
        return $this->hasMany(TourPackageItinerary::class)
            ->orderBy('day_number');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TourPackageDayActivity::class)
            ->orderBy('order');
    }

    public function information(): HasMany
    {
        return $this->hasMany( TourPackageInformation::class, 'tour_package_id' );
    }

    public function offers(): HasMany
    {
        return $this->hasMany( TourPackageOffer::class, 'tour_package_id' );
    }

     public function hotels(): HasMany
    {
        return $this->hasMany( TourPackageOfferHotel::class, 'tour_package_offer_id' );
    }
}
