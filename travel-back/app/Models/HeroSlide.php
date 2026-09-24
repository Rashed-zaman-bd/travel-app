<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class HeroSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'com_name',
        'image',
        'cta_text',
        'cta_url',
        'photo_text',
        'author_name',
        'location',
        'photo_date',
        'order',
        'is_active',
    ];

    // Define columns that hold localized JSON data
    public array $translatable = [
        'title',
        'description',
        'com_name',
        'cta_text',
        'photo_text',
        'location',
        'author_name',
        'photo_date',
    ];

    protected $casts = [
        'title'       => 'array',
        'description' => 'array',
        'com_name'    => 'array',
        'cta_text'    => 'array',
        'photo_text'  => 'array',
        'location'    => 'array',
        'author_name' => 'array',
        'photo_date'  => 'array',
        'is_active'   => 'boolean',
        'order'       => 'integer',
    ];
}
