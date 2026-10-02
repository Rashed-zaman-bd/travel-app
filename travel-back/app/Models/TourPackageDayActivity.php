<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourPackageDayActivity extends Model
{
    use HasFactory;

    protected $table = 'tour_package_day_activities';

    protected $fillable = [
        'tour_package_itinerary_id',
        'tour_package_id',
        'title',
        'description',
        'image',
        'image_caption',
        'order',
        'is_active',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'image_caption' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function day(): BelongsTo
    {
        return $this->belongsTo(TourPackageItinerary::class, 'tour_package_itinerary_id');
    }

    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class);
    }
}
