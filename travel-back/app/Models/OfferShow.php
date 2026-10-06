<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferShow extends Model
{
    use HasFactory;

    protected $table = 'offer_shows';

    protected $fillable = [
        'title',
        'description',
        'location',
        'price',
        'payment_method',
        'discount',
        'tour_duration',
        'image',
        'url',
        'order',
        'is_active',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'location' => 'array',
        'payment_method' => 'array',
        'discount' => 'array',
        'tour_duration' => 'array',
        'price' => 'decimal:2',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];
}
