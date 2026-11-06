<?php

namespace App\Contracts;

use App\Models\MonitoringTask;

interface AppointmentMonitoringProvider
{
    public function checkAvailability(MonitoringTask $task): array;

    public function submitChallengeResponse(MonitoringTask $task, string $response): bool;

    public function getProviderName(): string;

    public function isLiveIntegration(): bool;
}
