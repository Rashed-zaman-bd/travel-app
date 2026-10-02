<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourPackageItinerary extends Model
{
    use HasFactory;

    protected $table = 'tour_package_itinerarys';

    protected $fillable = [
        'tour_package_id',
        'day_number',
        'highlights',
        'overnight',
        'description',
        'map_image',
        'image_caption',
        'order',
        'is_active',
    ];

    protected $casts = [
        'highlights' => 'array',
        'overnight' => 'array',
        'description' => 'array',
        'image_caption' => 'array',
        'is_active' => 'boolean',
        'day_number' => 'integer',
        'order' => 'integer',
    ];

    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TourPackageDayActivity::class)
            ->orderBy('order');
    }
}