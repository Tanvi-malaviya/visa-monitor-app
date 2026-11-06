<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonitoringJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'applicant_id',
        'status',
        'interval_minutes',
        'last_checked_at',
        'next_check_at',
        'checks_count',
        'session_status',
        'pending_challenge_type',
        'pending_challenge_data',
        'notes',
    ];

    protected $casts = [
        'last_checked_at' => 'datetime',
        'next_check_at' => 'datetime',
        'interval_minutes' => 'integer',
        'checks_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function appointmentSlots(): HasMany
    {
        return $this->hasMany(AppointmentSlot::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
