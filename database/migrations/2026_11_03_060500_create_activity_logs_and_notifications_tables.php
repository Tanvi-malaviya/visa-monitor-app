<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('monitoring_task_id')->nullable()->constrained()->nullOnDelete();
                $table->string('level', 20)->default('info');
                $table->string('action', 80);
                $table->text('details');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('type', 60);
                $table->string('title');
                $table->text('message');
                $table->boolean('is_read')->default(false);
                $table->string('action_url')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('activity_logs');
    }
};
