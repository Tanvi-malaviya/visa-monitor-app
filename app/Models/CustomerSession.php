<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerSession extends Model
{
    protected $fillable = ['user_id', 'monitoring_task_id', 'portal_name', 'session_status', 'last_active_at', 'expires_at'];

    protected $casts = [
        'last_active_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(MonitoringTask::class, 'monitoring_task_id');
    }
}
