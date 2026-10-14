<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaAssistanceRequest extends Model
{
    use HasFactory;

    protected $table = 'visa_assistance_requests';

    protected $fillable = [
        'visa_service_id',
        'service_name',
        'name',
        'phone',
        'email',
        'message',
        'status',
    ];

    protected $casts = [
        'visa_service_id' => 'integer',
    ];

    /**
     * Get the visa service associated with this assistance request.
     */
    public function visaService(): BelongsTo
    {
        return $this->belongsTo(VisaService::class);
    }
}
