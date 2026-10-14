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
        'visa_service_id',
        'applicant_category',
        'document_name',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'applicant_category' => 'array',
        'document_name' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the visa service associated with the document.
     */
    public function visaService(): BelongsTo
    {
        return $this->belongsTo(VisaService::class);
    }

    
}