<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaFeeOption extends Model
{
    use HasFactory;

    protected $table = 'visa_fee_options';

    protected $fillable = [
        'visa_service_id',
        'option_name',
        'eligibility_description',
        'fee',
        'processing_time',
        'interview_required',
        'is_active',
    ];

    protected $casts = [
        'option_name' => 'array',
        'eligibility_description' => 'array',
        'processing_time' => 'array',
        'fee' => 'decimal:2',
        'interview_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the visa service associated with this fee option.
     */
    public function visaService(): BelongsTo
    {
        return $this->belongsTo(VisaService::class);
    }
}