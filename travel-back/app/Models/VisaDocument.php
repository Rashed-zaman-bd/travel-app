<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaDocument extends Model
{
    use HasFactory;

    protected $table = 'visa_documents';

    protected $fillable = [
        'visa_country_id',
        'visa_type_id',
        'visa_document_category_id',
        'document_name',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'document_name' => 'array',
        'description'   => 'array',
        'is_active'     => 'boolean',
        'sort_order'    => 'integer',
    ];

    public function visaCountry(): BelongsTo
    {
        return $this->belongsTo(VisaCountry::class, 'visa_country_id');
    }

    public function visaType(): BelongsTo
    {
        return $this->belongsTo(VisaType::class, 'visa_type_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(VisaDocumentCategory::class, 'visa_document_category_id');
    }
}