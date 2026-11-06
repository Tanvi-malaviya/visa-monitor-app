<?php

namespace App\Http\Controllers;

use App\Contracts\AppointmentMonitoringProvider;
use App\Models\ActivityLog;
use App\Models\Applicant;
use App\Models\AppointmentOpportunity;
use App\Models\AppointmentPreference;
use App\Models\MonitoringSchedule;
use App\Models\MonitoringTask;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    protected AppointmentMonitoringProvider $provider;

    public function __construct(AppointmentMonitoringProvider $provider)
    {
        $this->provider = $provider;
    }

    public function dashboard()
    {
        $user = Auth::user();
        $applicant = $user->applicants()->with(['preference', 'monitoringTask.schedule'])->first();

        if (! $applicant) {
            $nameParts = explode(' ', $user->name, 2);
            $applicant = Applicant::create([
                'user_id' => $user->id,
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? 'Applicant',
                'passport_number' => 'Z'.rand(1000000, 9999999),
                'passport_expiry_date' => now()->addYears(7),
                'ds160_confirmation_number' => 'AA00'.rand(100000, 999999),
                'nationality' => 'Indian',
                'status' => 'active',
            ]);

            AppointmentPreference::create([
                'applicant_id' => $applicant->id,
                'visa_category' => 'B1/B2 - Business & Tourism',
                'preferred_locations' => ['Mumbai', 'New Delhi', 'Hyderabad'],
                'earliest_date' => now()->addDays(15),
                'latest_date' => now()->addDays(90),
                'notify_email' => true,
                'notify_whatsapp' => false,
            ]);
        }

        $task = MonitoringTask::firstOrCreate(
            ['applicant_id' => $applicant->id],
            [
                'user_id' => $user->id,
                'status' => 'active',
                'action_required_type' => 'none',
                'last_check_at' => now(),
                'last_successful_check_at' => now(),
                'next_check_at' => now()->addMinutes(5),
                'consecutive_failures' => 0,
                'total_checks_run' => 0,
            ]
        );

        $schedule = MonitoringSchedule::firstOrCreate(
            ['monitoring_task_id' => $task->id],
            [
                'interval_minutes' => 5,
                'is_active' => true,
                'pause_during_night' => false,
            ]
        );

        $opportunities = AppointmentOpportunity::where('monitoring_task_id', $task->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $logs = $task->logs()->orderBy('created_at', 'desc')->take(15)->get();
        if ($logs->isEmpty()) {
            $logs = ActivityLog::where('user_id', $user->id)->orderBy('created_at', 'desc')->take(15)->get();
        }

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $pendingChallenge = $task->action_required_data;

        return view('customer.dashboard', compact('user', 'applicant', 'task', 'schedule', 'opportunities', 'logs', 'notifications', 'pendingChallenge'));
    }

    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        $applicant = $user->applicants()->firstOrFail();
        $task = $applicant->monitoringTask()->firstOrFail();
        $preference = $applicant->preference;

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'passport_number' => ['required', 'string', 'max:50'],
            'ds160_confirmation_number' => ['required', 'string', 'max:50'],
            'visa_category' => ['required', 'string'],
            'preferred_locations' => ['required', 'array', 'min:1'],
            'earliest_date' => ['nullable', 'date'],
            'latest_date' => ['nullable', 'date', 'after_or_equal:earliest_date'],
            'interval_minutes' => ['required', 'integer', 'in:3,5,10,15,30,60'],
        ]);

        $applicant->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'passport_number' => $validated['passport_number'],
            'ds160_confirmation_number' => $validated['ds160_confirmation_number'],
        ]);

        $preference->update([
            'visa_category' => $validated['visa_category'],
            'preferred_locations' => $validated['preferred_locations'],
            'earliest_date' => $validated['earliest_date'],
            'latest_date' => $validated['latest_date'],
        ]);

        $task->schedule()->update([
            'interval_minutes' => $validated['interval_minutes'],
        ]);

        $task->update([
            'next_check_at' => now()->addMinutes($validated['interval_minutes']),
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'monitoring_task_id' => $task->id,
            'level' => 'info',
            'action' => 'PREFERENCES_UPDATED',
            'details' => "Updated preferences: {$validated['visa_category']} across ".implode(', ', $validated['preferred_locations']).". Scan interval: {$validated['interval_minutes']}m.",
        ]);

        return back()->with('success', 'Visa monitoring preferences and schedule updated successfully.');
    }

    public function toggleMonitoring()
    {
        $user = Auth::user();
        $applicant = $user->applicants()->firstOrFail();
        $task = $applicant->monitoringTask()->firstOrFail();

        if ($task->status === 'active') {
            $task->status = 'paused';
            $message = 'Automated appointment monitoring paused.';
            $level = 'warning';
        } else {
            $task->status = 'active';
            $task->next_check_at = now()->addMinutes($task->schedule->interval_minutes ?? 5);
            $message = 'Automated appointment monitoring resumed.';
            $level = 'success';
        }
        $task->save();

        ActivityLog::create([
            'user_id' => $user->id,
            'monitoring_task_id' => $task->id,
            'level' => $level,
            'action' => 'MONITORING_TOGGLED',
            'details' => $message,
        ]);

        return back()->with('success', $message);
    }

    public function runManualCheck()
    {
        $user = Auth::user();
        $applicant = $user->applicants()->firstOrFail();
        $task = $applicant->monitoringTask()->firstOrFail();

        $result = $this->provider->checkAvailability($task);

        $msg = $result['message'] ?? 'Monitoring check completed successfully.';

        return back()->with('success', $msg);
    }

    public function submitOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string'],
        ]);

        $user = Auth::user();
        $applicant = $user->applicants()->firstOrFail();
        $task = $applicant->monitoringTask()->firstOrFail();

        $success = $this->provider->submitChallengeResponse($task, $request->input('otp'));

        if ($success) {
            ActivityLog::create([
                'user_id' => $user->id,
                'monitoring_task_id' => $task->id,
                'level' => 'success',
                'action' => 'OTP_VERIFIED',
                'details' => 'Customer provided valid OTP. Authenticated session restored.',
            ]);

            return back()->with('success', 'Authentication verified! Session reconnected and monitoring resumed.');
        }

        return back()->with('error', 'Verification code is invalid. Please try again or use the test code shown.');
    }

    public function bookSlot(int $opportunityId)
    {
        $user = Auth::user();
        $slot = AppointmentOpportunity::findOrFail($opportunityId);
        $task = $slot->task;

        if ($task->user_id !== $user->id) {
            abort(403);
        }

        $slot->status = 'confirmed_booked';
        $slot->save();

        // Auto-stop monitoring rule from proposal
        $task->status = 'completed';
        $task->save();

        ActivityLog::create([
            'user_id' => $user->id,
            'monitoring_task_id' => $task->id,
            'level' => 'success',
            'action' => 'BOOKING_CONFIRMED',
            'details' => "Slot confirmed for {$slot->consulate_location} on {$slot->appointment_date->format('M d, Y')} at {$slot->appointment_time}. Monitoring completed.",
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'system',
            'title' => 'Visa Appointment Booking Confirmed',
            'message' => "Your appointment at {$slot->consulate_location} on {$slot->appointment_date->format('M d, Y')} has been confirmed. Background monitoring has automatically completed.",
        ]);

        return back()->with('success', 'Appointment marked as booked! Automated monitoring has stopped as per schedule rules.');
    }

    public function markAllNotificationsRead()
    {
        Auth::user()->notifications()->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
