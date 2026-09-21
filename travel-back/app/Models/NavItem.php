<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'title',
        'url',
        'icon',
        'order',
        'is_active',
        'open_new_tab',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
        'open_new_tab' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(NavItem::class, 'parent_id')
            ->orderBy('order');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()
            ->with('childrenRecursive');
    }
}