<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentPreference extends Model
{
    protected $fillable = [
        'applicant_id', 'visa_category', 'preferred_locations',
        'earliest_date', 'latest_date', 'notify_email', 'notify_whatsapp', 'whatsapp_number',
    ];

    protected $casts = [
        'preferred_locations' => 'array',
        'earliest_date' => 'date',
        'latest_date' => 'date',
        'notify_email' => 'boolean',
        'notify_whatsapp' => 'boolean',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
