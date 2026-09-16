<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TopBanner extends Model
{
    protected $table = 'top_banners';

    protected $fillable = ['title', 'image', 'order', 'active'];

    protected $casts = [
        'active' => 'boolean',
        'order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true)->orderBy('order');
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (! $value) {
                    return null;
                }

                try {
                    return Storage::disk('public')->url($value);
                } catch (\Throwable $e) {
                    return rtrim(config('app.url'), '/') . '/storage/' . ltrim($value, '/');
                }
            },
        );
    }
}