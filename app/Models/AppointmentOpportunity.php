<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentOpportunity extends Model
{
    protected $fillable = [
        'monitoring_task_id', 'consulate_location', 'visa_category',
        'appointment_date', 'appointment_time', 'available_slots', 'status', 'detected_at',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'detected_at' => 'datetime',
        'available_slots' => 'integer',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(MonitoringTask::class, 'monitoring_task_id');
    }
}
