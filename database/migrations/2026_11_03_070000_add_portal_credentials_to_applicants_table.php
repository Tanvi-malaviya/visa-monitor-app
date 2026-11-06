<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('portal_username')->nullable()->after('ds160_confirmation_number');
            $table->string('portal_password')->nullable()->after('portal_username');
            $table->string('security_q1')->nullable()->after('portal_password');
            $table->string('security_a1')->nullable()->after('security_q1');
            $table->string('security_q2')->nullable()->after('security_a1');
            $table->string('security_a2')->nullable()->after('security_q2');
            $table->string('security_q3')->nullable()->after('security_a2');
            $table->string('security_a3')->nullable()->after('security_q3');
            $table->date('date_of_birth')->nullable()->after('security_a3');
            $table->string('delivery_address')->nullable()->after('date_of_birth');
            $table->string('city')->nullable()->after('delivery_address');
            $table->string('pincode')->nullable()->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
