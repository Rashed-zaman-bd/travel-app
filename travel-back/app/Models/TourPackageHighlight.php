<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourPackageHighlight extends Model
{
    use HasFactory;

    protected $table = 'tour_package_highlights';

    protected $fillable = [
        'tour_package_id',
        'highlight',
        'icon',
        'order',
        'is_active',
    ];

    protected $casts = [
        'highlight' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class);
    }
}
