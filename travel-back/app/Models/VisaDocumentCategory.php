<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisaDocumentCategory extends Model
{
    use HasFactory;

    protected $table = 'visa_document_categories';
    protected $fillable = ['visa_service_id', 'title', 'is_active', 'sort_order'];

    protected $casts = [
        'title'      => 'array',
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function visaService(): BelongsTo
    {
        return $this->belongsTo(VisaService::class, 'visa_service_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VisaDocument::class, 'visa_document_category_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
