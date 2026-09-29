<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'sub_title',
        'slug',
        'image',
        'image_title',
        'destination_name',
        'destination_hero_image',
        'destination_hero_title',
        'destination_hero_btn',
        'destination_title',
        'destination_sub_title',
        'tour_slug',
        'tour_title',
        'map_image',
        'tour_description',
         'icon',
        'order',
        'is_active',
    ];

    public array $translatable = [
        'title',
        'sub_title',
        'image_title',
        'destination_name',
        'destination_hero_title',
        'destination_hero_btn',
        'destination_title',
        'destination_sub_title',
        'tour_title',
        'tour_description',
    ];
  
    protected $casts = [
        'title' => 'array',
        'sub_title' => 'array',
        'image' => 'array',
        'image_title' => 'array',
        'destination_name' => 'array',
        'destination_hero_image' => 'array',
        'destination_hero_title' => 'array',
        'destination_hero_btn' => 'array',
        'destination_title' => 'array',
        'destination_sub_title' => 'array',
        'tour_title' => 'array',
        'map_image' => 'array',
        'tour_description' => 'array',
        'is_active'   => 'boolean',
        'order'       => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    
}