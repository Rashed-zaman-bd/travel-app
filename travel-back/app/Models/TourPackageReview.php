<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourPackageReview extends Model
{
    protected $table = 'tour_package_reviews';

    protected $fillable = [
        'tour_package_id',
        'user_id',
        'rating',
        'comment',
        'traveler_location',
        'destination_visited',
        'travel_type',
        'travel_date',
        'is_approved',
    ];

    protected $casts = [
        'rating'      => 'integer',
        'travel_date' => 'date',
        'is_approved' => 'boolean',
    ];

    /* -------------------------------------------------------------------------- */
    /*                                Relationships                               */
    /* -------------------------------------------------------------------------- */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class);
    }

    /* -------------------------------------------------------------------------- */
    /*                                Query Scopes                                */
    /* -------------------------------------------------------------------------- */

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    /* -------------------------------------------------------------------------- */
    /*                                  Accessors                                 */
    /* -------------------------------------------------------------------------- */

    public function formattedTravelInfo(): Attribute
    {
        return Attribute::make(
            get: function () {
                $parts = [];

                if ($this->destination_visited) {
                    $parts[] = "Traveled to {$this->destination_visited}";
                }

                if ($this->travel_type) {
                    $parts[] = "as {$this->travel_type}";
                }

                if ($this->travel_date) {
                    $parts[] = "in " . $this->travel_date->format('F, Y');
                }

                return implode(' ', $parts);
            }
        );
    }
}