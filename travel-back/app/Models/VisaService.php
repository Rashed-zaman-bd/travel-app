<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisaService extends Model
{
    use HasFactory;

    protected $table = 'visa_services';

    protected $fillable = [
        'visa_country_id',
        'visa_type_id',
        'title',
        'description',
        'processing_time',
        'currency',
        'base_fee',
        'eligibility',
        'application_url',
        'is_active',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'processing_time' => 'array',
        'currency' => 'array',
        'eligibility' => 'array',
        'base_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function visaCountry(): BelongsTo
    {
        return $this->belongsTo(VisaCountry::class, 'visa_country_id');
    }

    public function visaType(): BelongsTo
    {
        return $this->belongsTo(VisaType::class, 'visa_type_id');
    }

    public function visaDocuments(): HasMany
    {
        return $this->hasMany(VisaDocument::class, 'visa_service_id');
    }

    public function visaFees(): HasMany
    {
        return $this->hasMany(VisaFeeOption::class, 'visa_service_id');
    }

    public function visaAssistances(): HasMany
    {
        return $this->hasMany(VisaAssistanceRequest::class, 'visa_service_id');
    }
}