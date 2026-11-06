<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Applicant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'passport_number',
        'passport_expiry_date',
        'ds160_confirmation_number',
        'nationality',
        'status',
        'portal_username',
        'portal_password',
        'security_q1',
        'security_a1',
        'security_q2',
        'security_a2',
        'security_q3',
        'security_a3',
        'date_of_birth',
        'delivery_address',
        'city',
        'pincode',
    ];

    protected $casts = [
        'passport_expiry_date' => 'date',
        'date_of_birth' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function preference(): HasOne
    {
        return $this->hasOne(AppointmentPreference::class);
    }

    public function monitoringTask(): HasOne
    {
        return $this->hasOne(MonitoringTask::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function hasPortalCredentials(): bool
    {
        return ! empty($this->portal_username) && ! empty($this->portal_password);
    }
}
