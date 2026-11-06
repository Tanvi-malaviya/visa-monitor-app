<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitoringSchedule extends Model
{
    protected $fillable = ['monitoring_task_id', 'interval_minutes', 'is_active', 'pause_during_night'];

    protected $casts = [
        'is_active' => 'boolean',
        'pause_during_night' => 'boolean',
        'interval_minutes' => 'integer',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(MonitoringTask::class, 'monitoring_task_id');
    }
}
