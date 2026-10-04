<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourPackageOffer extends Model
{
    use HasFactory;

    protected $table = 'tour_package_offers';

    protected $fillable = [
        'tour_package_id',
        'offer_name',
        'valid_from',
        'valid_till',
        'departs',
        'price',
        'price_label',
        'price_note',
        'order',
        'is_active',
    ];

    protected $casts = [
        'offer_name' => 'array',
        'departs' => 'array',
        'price_label' => 'array',
        'price_note' => 'array',
        'valid_from' => 'date',
        'valid_till' => 'date',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo( TourPackage::class, 'tour_package_id' );
    }

    public function hotels(): HasMany
    {
        return $this->hasMany( TourPackageOfferHotel::class, 'tour_package_offer_id' );
    }
}