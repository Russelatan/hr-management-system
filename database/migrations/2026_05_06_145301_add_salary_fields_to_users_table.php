<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('basic_salary', 10, 2)->nullable()->after('employment_type');
            $table->decimal('sss_contribution', 8, 2)->nullable()->after('basic_salary');
            $table->decimal('philhealth_contribution', 8, 2)->nullable()->after('sss_contribution');
            $table->decimal('pagibig_contribution', 8, 2)->nullable()->after('philhealth_contribution');
            $table->decimal('other_deductions', 8, 2)->default(0)->after('pagibig_contribution');
            $table->unsignedTinyInteger('working_days_per_month')->default(22)->after('other_deductions');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'basic_salary',
                'sss_contribution',
                'philhealth_contribution',
                'pagibig_contribution',
                'other_deductions',
                'working_days_per_month',
            ]);
        });
    }
};
