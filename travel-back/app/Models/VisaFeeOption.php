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
        'visa_country_id',
        'visa_type_id',
        'name',
        'description',
        'fee',
        'is_active',
    ];

     protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'fee' => 'decimal:2',
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


}