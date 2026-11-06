<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitoringLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['monitoring_task_id', 'level', 'event_type', 'message', 'context_data', 'execution_duration_ms', 'created_at'];

    protected $casts = [
        'context_data' => 'array',
        'created_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(MonitoringTask::class, 'monitoring_task_id');
    }
}
