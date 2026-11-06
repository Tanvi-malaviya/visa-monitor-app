<?php

namespace App\Services\Providers;

use App\Contracts\AppointmentMonitoringProvider;
use App\Models\AppointmentOpportunity;
use App\Models\MonitoringLog;
use App\Models\MonitoringTask;
use Carbon\Carbon;

class SimulatedAppointmentMonitoringProvider implements AppointmentMonitoringProvider
{
    public function getProviderName(): string
    {
        return 'Simulated Provider (Safe Sandbox)';
    }

    public function isLiveIntegration(): bool
    {
        return false;
    }

    public function checkAvailability(MonitoringTask $task): array
    {
        $startTime = microtime(true);
        $applicant = $task->applicant;
        $pref = $applicant->preference;
        $locations = $pref->preferred_locations ?? ['Mumbai', 'New Delhi'];

        $roll = rand(1, 100);

        if ($roll <= 10 && $task->status === 'active' && $task->total_checks_run > 0) {
            $otp = (string) rand(112233, 998877);
            $task->update([
                'status' => 'awaiting_action',
                'action_required_type' => 'otp',
                'action_required_data' => [
                    'prompt' => 'Official portal requested 6-digit MFA security code sent to registered contact.',
                    'test_otp' => $otp,
                    'issued_at' => now()->toIso8601String(),
                ],
            ]);

            MonitoringLog::create([
                'monitoring_task_id' => $task->id,
                'level' => 'warning',
                'event_type' => 'OTP_CHALLENGE',
                'message' => 'Portal triggered authentication checkpoint. Customer action required.',
                'execution_duration_ms' => round((microtime(true) - $startTime) * 1000),
            ]);

            return ['status' => 'awaiting_action', 'action' => 'otp', 'code' => $otp];
        }

        if ($roll >= 85) {
            $consulate = $locations[array_rand($locations)];
            $slotDate = Carbon::now()->addDays(rand(15, 60));

            $opportunity = AppointmentOpportunity::create([
                'monitoring_task_id' => $task->id,
                'consulate_location' => $consulate,
                'visa_category' => $pref->visa_category ?? 'B1/B2',
                'appointment_date' => $slotDate->format('Y-m-d'),
                'appointment_time' => '10:15 AM',
                'available_slots' => rand(2, 5),
                'status' => 'available',
                'detected_at' => now(),
            ]);

            $task->update(['status' => 'slot_found']);

            MonitoringLog::create([
                'monitoring_task_id' => $task->id,
                'level' => 'success',
                'event_type' => 'SLOT_DETECTED',
                'message' => "Appointment opening detected at {$consulate} on {$slotDate->format('M d, Y')}.",
                'context_data' => ['opportunity_id' => $opportunity->id],
                'execution_duration_ms' => round((microtime(true) - $startTime) * 1000),
            ]);

            return ['status' => 'slot_found', 'opportunity' => $opportunity];
        }

        $task->update([
            'last_check_at' => now(),
            'last_successful_check_at' => now(),
            'next_check_at' => now()->addMinutes($task->schedule->interval_minutes ?? 5),
            'total_checks_run' => $task->total_checks_run + 1,
            'consecutive_failures' => 0,
        ]);

        MonitoringLog::create([
            'monitoring_task_id' => $task->id,
            'level' => 'info',
            'event_type' => 'CHECK_COMPLETED',
            'message' => 'Completed scan for '.implode(', ', $locations).'. No openings matching criteria.',
            'execution_duration_ms' => round((microtime(true) - $startTime) * 1000),
        ]);

        return ['status' => 'active', 'message' => 'No slots available'];
    }

    public function submitChallengeResponse(MonitoringTask $task, string $response): bool
    {
        $challenge = $task->action_required_data ?? [];
        $expected = $challenge['test_otp'] ?? '123456';

        if ($response === $expected || $response === '123456') {
            $task->update([
                'status' => 'active',
                'action_required_type' => 'none',
                'action_required_data' => null,
            ]);

            MonitoringLog::create([
                'monitoring_task_id' => $task->id,
                'level' => 'success',
                'event_type' => 'OTP_RESOLVED',
                'message' => 'Customer submitted valid OTP. Active monitoring resumed.',
            ]);

            return true;
        }

        return false;
    }
}
