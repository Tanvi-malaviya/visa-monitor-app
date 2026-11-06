<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MonitoringTask extends Model
{
    protected $fillable = [
        'user_id', 'applicant_id', 'status', 'action_required_type',
        'action_required_data', 'last_check_at', 'last_successful_check_at',
        'next_check_at', 'consecutive_failures', 'total_checks_run',
    ];

    protected $casts = [
        'last_check_at' => 'datetime',
        'last_successful_check_at' => 'datetime',
        'next_check_at' => 'datetime',
        'action_required_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function schedule(): HasOne
    {
        return $this->hasOne(MonitoringSchedule::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(MonitoringLog::class);
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(AppointmentOpportunity::class);
    }
}
