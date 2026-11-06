<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\AppointmentSlot;
use App\Models\MonitoringJob;
use App\Models\Notification;
use Carbon\Carbon;

class MonitoringSimulatorService
{
    /**
     * Run a scheduled check for a specific monitoring job
     */
    public function runCheck(MonitoringJob $job): array
    {
        if ($job->status === 'paused' || $job->status === 'completed') {
            return [
                'status' => $job->status,
                'message' => "Monitoring job is currently {$job->status}.",
            ];
        }

        if ($job->status === 'awaiting_otp') {
            return [
                'status' => 'awaiting_otp',
                'message' => 'Pending customer OTP verification. Scheduled check delayed.',
            ];
        }

        if ($job->status === 'awaiting_captcha') {
            return [
                'status' => 'awaiting_captcha',
                'message' => 'Pending customer CAPTCHA resolution.',
            ];
        }

        $now = Carbon::now();
        $job->last_checked_at = $now;
        $job->next_check_at = $now->copy()->addMinutes($job->interval_minutes);
        $job->checks_count += 1;

        $applicant = $job->applicant;
        $locations = $applicant->preferred_locations ?? ['Mumbai', 'New Delhi', 'Hyderabad'];

        // Randomly simulate an occasional OTP challenge (e.g. 1 out of 10 checks) if healthy
        $roll = rand(1, 100);

        if ($roll <= 12 && $job->checks_count > 1 && $job->status === 'active') {
            return $this->triggerOtpChallenge($job);
        }

        // Randomly simulate a slot drop (15% chance in simulation)
        if ($roll >= 85) {
            $randomConsulate = $locations[array_rand($locations)];
            $randomDays = rand(10, 45);
            $slotDate = Carbon::now()->addDays($randomDays);

            return $this->registerDiscoveredSlot($job, $randomConsulate, $slotDate->format('Y-m-d'), '09:45 AM', rand(2, 6));
        }

        // Normal check completed: no slots currently
        $scannedConsulates = implode(', ', $locations);
        $job->session_status = 'valid';
        $job->save();

        ActivityLog::create([
            'user_id' => $job->user_id,
            'monitoring_job_id' => $job->id,
            'level' => 'info',
            'action' => 'CHECK_COMPLETED',
            'details' => "Automated check completed at [{$now->format('H:i:s')}]. Consulates scanned: {$scannedConsulates}. No open slots meeting criteria.",
        ]);

        return [
            'status' => 'active',
            'message' => "Check successful. No open slots found in {$scannedConsulates}.",
            'last_checked' => $now->toIso8601String(),
        ];
    }

    /**
     * Trigger an OTP Challenge requiring user input
     */
    public function triggerOtpChallenge(MonitoringJob $job): array
    {
        $otp = (string) rand(112233, 998877);

        $job->status = 'awaiting_otp';
        $job->session_status = 'otp_challenge';
        $job->pending_challenge_type = 'otp';
        $job->pending_challenge_data = json_encode([
            'otp' => $otp,
            'prompt' => 'Official portal requested 6-digit MFA security code sent to registered mobile/email.',
            'generated_at' => Carbon::now()->toIso8601String(),
        ]);
        $job->save();

        ActivityLog::create([
            'user_id' => $job->user_id,
            'monitoring_job_id' => $job->id,
            'level' => 'warning',
            'action' => 'OTP_CHALLENGE_REQUIRED',
            'details' => 'USVisaScheduling session triggered a two-factor authentication challenge. Customer action required.',
        ]);

        Notification::create([
            'user_id' => $job->user_id,
            'type' => 'otp_prompt',
            'title' => '⚠️ Action Required: US Visa Portal OTP Verification',
            'message' => "The automated monitoring worker encountered an authentication checkpoint. Please enter the OTP code (Test OTP: {$otp}) to continue scanning.",
            'action_url' => route('customer.dashboard'),
        ]);

        return [
            'status' => 'awaiting_otp',
            'challenge_type' => 'otp',
            'simulated_otp' => $otp,
            'message' => 'Session challenge triggered. Customer notified.',
        ];
    }

    /**
     * Submit OTP and restore session
     */
    public function verifyOtp(MonitoringJob $job, string $enteredOtp): bool
    {
        $challenge = json_decode($job->pending_challenge_data ?? '{}', true);
        $expectedOtp = $challenge['otp'] ?? null;

        // Allow matching or test fallback '123456'
        if ($enteredOtp === $expectedOtp || $enteredOtp === '123456' || empty($expectedOtp)) {
            $job->status = 'active';
            $job->session_status = 'valid';
            $job->pending_challenge_type = null;
            $job->pending_challenge_data = null;
            $job->save();

            ActivityLog::create([
                'user_id' => $job->user_id,
                'monitoring_job_id' => $job->id,
                'level' => 'success',
                'action' => 'OTP_VERIFIED',
                'details' => 'Customer provided valid OTP. Authenticated session restored. Resuming active monitoring jobs.',
            ]);

            Notification::create([
                'user_id' => $job->user_id,
                'type' => 'system',
                'title' => 'Session Authenticated',
                'message' => 'Your visa monitoring session has been re-authenticated successfully. Active monitoring resumed.',
            ]);

            return true;
        }

        ActivityLog::create([
            'user_id' => $job->user_id,
            'monitoring_job_id' => $job->id,
            'level' => 'danger',
            'action' => 'OTP_FAILED',
            'details' => "Customer entered incorrect OTP ({$enteredOtp}).",
        ]);

        return false;
    }

    /**
     * Force or register a discovered slot
     */
    public function registerDiscoveredSlot(MonitoringJob $job, string $consulate, string $date, string $time = '10:15 AM', int $count = 4): array
    {
        $slot = AppointmentSlot::create([
            'monitoring_job_id' => $job->id,
            'consulate' => $consulate,
            'visa_class' => $job->applicant->visa_class,
            'appointment_date' => $date,
            'appointment_time' => $time,
            'slots_count' => $count,
            'status' => 'available',
            'detected_at' => Carbon::now(),
        ]);

        $job->status = 'slot_found';
        $job->save();

        ActivityLog::create([
            'user_id' => $job->user_id,
            'monitoring_job_id' => $job->id,
            'level' => 'success',
            'action' => 'SLOT_DETECTED',
            'details' => "🎯 NEW APPOINTMENT DETECTED: {$consulate} Consulate on ".Carbon::parse($date)->format('M d, Y')." at {$time} ({$count} slots available).",
        ]);

        Notification::create([
            'user_id' => $job->user_id,
            'type' => 'slot_alert',
            'title' => "🎉 Slot Available: {$consulate} ({$job->applicant->visa_class})",
            'message' => "An appointment opening has been detected at {$consulate} on ".Carbon::parse($date)->format('M d, Y')." at {$time}. Log in immediately or click below to proceed.",
            'action_url' => route('customer.dashboard'),
        ]);

        return [
            'status' => 'slot_found',
            'slot_id' => $slot->id,
            'consulate' => $consulate,
            'date' => $date,
            'time' => $time,
            'slots_count' => $count,
            'message' => "High-priority slot detected at {$consulate}!",
        ];
    }
}
