<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourPackageOfferHotel extends Model
{
    use HasFactory;

    protected $table = 'tour_package_offer_hotels';

    protected $fillable = [
        'tour_package_id',
        'tour_package_offer_id',
        'hotel_name',
        'location',
        'order',
        'is_active',
    ];

    protected $casts = [
        'hotel_name' => 'array',
        'location' => 'array',
        'is_active' => 'boolean',
    ];


    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo( TourPackage::class, 'tour_package_id' );
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo( TourPackageOffer::class, 'tour_package_offer_id' );
    }

    public function tourPackageOffer(): BelongsTo
    {
        return $this->belongsTo(TourPackageOffer::class, 'tour_package_offer_id');
    }
}