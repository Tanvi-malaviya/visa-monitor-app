<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\AppointmentOpportunity;
use App\Models\AppointmentPreference;
use App\Models\CustomerSession;
use App\Models\MonitoringLog;
use App\Models\MonitoringSchedule;
use App\Models\MonitoringTask;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. System Settings
        SystemSetting::create([
            'key' => 'simulated_provider_active',
            'value' => 'true',
            'type' => 'boolean',
            'description' => 'Use simulated appointment slot generator while live access is pending approval.',
        ]);

        SystemSetting::create([
            'key' => 'default_check_interval',
            'value' => '5',
            'type' => 'integer',
            'description' => 'Default background scan interval in minutes.',
        ]);

        // 2. Sathwara InfoTech Admin User
        $admin = User::create([
            'name' => 'Sathwara Admin',
            'email' => 'admin@sathwara.com',
            'phone' => '+91 98765 43210',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // 3. Customer 1: John Doe (Active B1/B2 monitoring with detected slot)
        $customer1 = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+1 (555) 234-5678',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'is_active' => true,
        ]);

        $applicant1 = Applicant::create([
            'user_id' => $customer1->id,
            'first_name' => 'Johnathon',
            'last_name' => 'Doe',
            'passport_number' => 'Z9182374',
            'passport_expiry_date' => Carbon::now()->addYears(7),
            'ds160_confirmation_number' => 'AA00982143',
            'nationality' => 'Indian',
            'status' => 'active',
        ]);

        $pref1 = AppointmentPreference::create([
            'applicant_id' => $applicant1->id,
            'visa_category' => 'B1/B2 - Business & Tourism',
            'preferred_locations' => ['Mumbai', 'New Delhi', 'Hyderabad'],
            'earliest_date' => Carbon::now()->addDays(20),
            'latest_date' => Carbon::now()->addDays(80),
            'notify_email' => true,
            'notify_whatsapp' => false,
        ]);

        $task1 = MonitoringTask::create([
            'user_id' => $customer1->id,
            'applicant_id' => $applicant1->id,
            'status' => 'active',
            'action_required_type' => 'none',
            'last_check_at' => Carbon::now()->subMinutes(2),
            'last_successful_check_at' => Carbon::now()->subMinutes(2),
            'next_check_at' => Carbon::now()->addMinutes(3),
            'consecutive_failures' => 0,
            'total_checks_run' => 146,
        ]);

        MonitoringSchedule::create([
            'monitoring_task_id' => $task1->id,
            'interval_minutes' => 5,
            'is_active' => true,
            'pause_during_night' => false,
        ]);

        CustomerSession::create([
            'user_id' => $customer1->id,
            'monitoring_task_id' => $task1->id,
            'portal_name' => 'USVisaScheduling',
            'session_status' => 'authenticated',
            'last_active_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addHours(2),
        ]);

        AppointmentOpportunity::create([
            'monitoring_task_id' => $task1->id,
            'consulate_location' => 'Mumbai',
            'visa_category' => 'B1/B2 - Business & Tourism',
            'appointment_date' => Carbon::now()->addDays(28),
            'appointment_time' => '10:15 AM',
            'available_slots' => 3,
            'status' => 'available',
            'detected_at' => Carbon::now()->subMinutes(15),
        ]);

        MonitoringLog::create([
            'monitoring_task_id' => $task1->id,
            'level' => 'success',
            'event_type' => 'SLOT_DETECTED',
            'message' => 'Appointment opening detected at Mumbai on '.Carbon::now()->addDays(28)->format('M d, Y').' (3 seats available).',
            'execution_duration_ms' => 420,
        ]);

        // 4. Customer 2: Priya Sharma (Awaiting OTP verification)
        $customer2 = User::create([
            'name' => 'Priya Sharma',
            'email' => 'priya.sharma@example.com',
            'phone' => '+91 94280 11223',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'is_active' => true,
        ]);

        $applicant2 = Applicant::create([
            'user_id' => $customer2->id,
            'first_name' => 'Priya',
            'last_name' => 'Sharma',
            'passport_number' => 'V5432190',
            'passport_expiry_date' => Carbon::now()->addYears(9),
            'ds160_confirmation_number' => 'AA00441920',
            'nationality' => 'Indian',
            'status' => 'active',
        ]);

        AppointmentPreference::create([
            'applicant_id' => $applicant2->id,
            'visa_category' => 'F-1 - Student',
            'preferred_locations' => ['Hyderabad', 'Chennai'],
            'earliest_date' => Carbon::now()->addDays(10),
            'latest_date' => Carbon::now()->addDays(60),
            'notify_email' => true,
            'notify_whatsapp' => true,
            'whatsapp_number' => '+919428011223',
        ]);

        $task2 = MonitoringTask::create([
            'user_id' => $customer2->id,
            'applicant_id' => $applicant2->id,
            'status' => 'awaiting_action',
            'action_required_type' => 'otp',
            'action_required_data' => [
                'prompt' => 'Official portal requested 6-digit MFA security code sent to registered mobile/email.',
                'test_otp' => '482910',
                'issued_at' => Carbon::now()->subMinutes(3)->toIso8601String(),
            ],
            'last_check_at' => Carbon::now()->subMinutes(3),
            'last_successful_check_at' => Carbon::now()->subMinutes(6),
            'next_check_at' => Carbon::now()->addMinutes(1),
            'consecutive_failures' => 0,
            'total_checks_run' => 89,
        ]);

        MonitoringSchedule::create([
            'monitoring_task_id' => $task2->id,
            'interval_minutes' => 3,
            'is_active' => true,
        ]);

        MonitoringLog::create([
            'monitoring_task_id' => $task2->id,
            'level' => 'warning',
            'event_type' => 'OTP_CHALLENGE',
            'message' => 'Portal triggered authentication checkpoint. Customer action required (Test OTP: 482910).',
            'execution_duration_ms' => 310,
        ]);
    }
}
