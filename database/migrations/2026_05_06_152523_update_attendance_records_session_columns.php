<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropColumn(['check_in_time', 'check_out_time']);
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            $table->time('morning_in')->nullable()->after('date');
            $table->time('morning_out')->nullable()->after('morning_in');
            $table->time('afternoon_in')->nullable()->after('morning_out');
            $table->time('afternoon_out')->nullable()->after('afternoon_in');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropColumn(['morning_in', 'morning_out', 'afternoon_in', 'afternoon_out']);
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            $table->time('check_in_time')->nullable()->after('date');
            $table->time('check_out_time')->nullable()->after('check_in_time');
        });
    }
};
