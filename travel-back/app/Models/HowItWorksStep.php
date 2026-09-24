<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HowItWorksStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'heading',
        'topline',
        'title',
        'description',
        'icon',
        'order',
        'is_active',
    ];

    public array $translatable = [
        'heading',
        'topline',
        'title',
        'description',
    ];

    protected $casts = [
        'heading'     => 'array',
        'topline'     => 'array',
        'title'       => 'array',
        'description' => 'array',
        'is_active'   => 'boolean',
        'order'       => 'integer',
    ];
}
