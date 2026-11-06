<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'monitoring_job_id',
        'consulate',
        'visa_class',
        'appointment_date',
        'appointment_time',
        'slots_count',
        'status',
        'detected_at',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'detected_at' => 'datetime',
        'slots_count' => 'integer',
    ];

    public function monitoringJob(): BelongsTo
    {
        return $this->belongsTo(MonitoringJob::class);
    }
}
