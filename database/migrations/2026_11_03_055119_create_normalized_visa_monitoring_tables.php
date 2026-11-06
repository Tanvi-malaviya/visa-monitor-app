<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update Users Table with Roles
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['customer', 'admin'])->default('customer')->after('email');
            }
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('role');
            }
            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('phone');
            }
        });

        // 2. Applicants Table
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('passport_number', 50)->index();
            $table->date('passport_expiry_date')->nullable();
            $table->string('ds160_confirmation_number', 50)->index();
            $table->string('nationality')->default('Indian');
            $table->enum('status', ['active', 'paused', 'booked', 'archived'])->default('active');
            $table->softDeletes();
            $table->timestamps();
        });

        // 3. Appointment Preferences Table (1-to-1 with Applicant)
        Schema::create('appointment_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('visa_category', 50); // e.g. B1/B2, F-1, H-1B, L-1
            $table->json('preferred_locations'); // ['Mumbai', 'New Delhi', 'Hyderabad']
            $table->date('earliest_date')->nullable();
            $table->date('latest_date')->nullable();
            $table->boolean('notify_email')->default(true);
            $table->boolean('notify_whatsapp')->default(false);
            $table->string('whatsapp_number', 25)->nullable();
            $table->timestamps();
        });

        // 4. Monitoring Tasks Table
        Schema::create('monitoring_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('applicant_id')->unique()->constrained()->cascadeOnDelete();
            $table->enum('status', ['active', 'paused', 'awaiting_action', 'slot_found', 'completed', 'failed'])->default('active');
            $table->enum('action_required_type', ['otp', 'captcha', 'reauth', 'none'])->nullable();
            $table->text('action_required_data')->nullable(); // JSON prompt or verification payload
            $table->timestamp('last_check_at')->nullable();
            $table->timestamp('last_successful_check_at')->nullable();
            $table->timestamp('next_check_at')->nullable();
            $table->unsignedInteger('consecutive_failures')->default(0);
            $table->unsignedInteger('total_checks_run')->default(0);
            $table->timestamps();
        });

        // 5. Monitoring Schedules Table (1-to-1 with Task)
        Schema::create('monitoring_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_task_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('interval_minutes')->default(5); // 3, 5, 10, 15, 30
            $table->boolean('is_active')->default(true);
            $table->boolean('pause_during_night')->default(false);
            $table->timestamps();
        });

        // 6. Monitoring Logs Table
        Schema::create('monitoring_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_task_id')->constrained()->cascadeOnDelete();
            $table->enum('level', ['info', 'success', 'warning', 'error'])->default('info');
            $table->string('event_type', 60); // CHECK_INITIATED, CHECK_COMPLETED, OTP_CHALLENGE, SLOT_DETECTED
            $table->text('message');
            $table->json('context_data')->nullable();
            $table->unsignedInteger('execution_duration_ms')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 7. Appointment Opportunities Table (Detected Slots)
        Schema::create('appointment_opportunities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_task_id')->constrained()->cascadeOnDelete();
            $table->string('consulate_location', 60); // Mumbai, New Delhi, Chennai, etc.
            $table->string('visa_category', 50);
            $table->date('appointment_date');
            $table->string('appointment_time', 20); // 09:30 AM
            $table->unsignedSmallInteger('available_slots')->default(1);
            $table->enum('status', ['available', 'notified', 'confirmed_booked', 'expired'])->default('available');
            $table->timestamp('detected_at');
            $table->timestamps();
        });

        // 8. Customer Sessions Table (USVisaScheduling Session Tracking)
        Schema::create('customer_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('monitoring_task_id')->nullable()->constrained()->nullOnDelete();
            $table->string('portal_name', 50)->default('USVisaScheduling');
            $table->enum('session_status', ['authenticated', 'expired', 'otp_challenge', 'captcha_challenge'])->default('authenticated');
            $table->timestamp('last_active_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // 9. System Settings Table
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string'); // boolean, integer, string, json
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('customer_sessions');
        Schema::dropIfExists('appointment_opportunities');
        Schema::dropIfExists('monitoring_logs');
        Schema::dropIfExists('monitoring_schedules');
        Schema::dropIfExists('monitoring_tasks');
        Schema::dropIfExists('appointment_preferences');
        Schema::dropIfExists('applicants');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'is_active']);
        });
    }
};
