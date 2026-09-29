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
    ];

  
    public array $translatable = [
        'title',
        'sub_title',
        'image',
        'image_title',
        'destination_name',
        'destination_hero_image',
        'destination_hero_title',
        'destination_hero_btn',
        'destination_title',
        'destination_sub_title',
        'tour_title',
        'map_image',
        'tour_description',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}