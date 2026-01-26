<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'employee'])->default('employee')->after('email');
            $table->string('employee_id')->unique()->nullable()->after('role');
            $table->string('phone')->nullable()->after('employee_id');
            $table->text('address')->nullable()->after('phone');
            $table->date('date_of_birth')->nullable()->after('address');
            $table->date('hire_date')->nullable()->after('date_of_birth');
            $table->enum('employment_status', ['active', 'on_leave', 'terminated', 'suspended'])->default('active')->after('hire_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'employee_id',
                'phone',
                'address',
                'date_of_birth',
                'hire_date',
                'employment_status',
            ]);
        });
    }
};
