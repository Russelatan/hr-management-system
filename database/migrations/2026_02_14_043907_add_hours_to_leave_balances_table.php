<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leave_balances', function (Blueprint $table) {
            $table->unsignedInteger('total_hours')->default(0)->after('remaining_days');
            $table->unsignedInteger('used_hours')->default(0)->after('total_hours');
            $table->unsignedInteger('remaining_hours')->default(0)->after('used_hours');
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE leave_balances DROP CONSTRAINT IF EXISTS leave_balances_leave_type_check');
            DB::statement("ALTER TABLE leave_balances ADD CONSTRAINT leave_balances_leave_type_check CHECK (leave_type::text = ANY (ARRAY['sick'::text, 'vacation'::text, 'personal'::text, 'other'::text, 'maternity-leave'::text, 'paternity-leave'::text, 'bereavement-leave'::text]))");
        }

        if ($driver === 'sqlite') {
            DB::transaction(function () {
                DB::statement("CREATE TABLE leave_balances_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    user_id INTEGER NOT NULL,
                    leave_type VARCHAR(50) NOT NULL CHECK(leave_type IN ('sick','vacation','personal','other','maternity-leave','paternity-leave','bereavement-leave')),
                    total_days INTEGER NOT NULL DEFAULT 0,
                    used_days INTEGER NOT NULL DEFAULT 0,
                    remaining_days INTEGER NOT NULL DEFAULT 0,
                    total_hours INTEGER NOT NULL DEFAULT 0,
                    used_hours INTEGER NOT NULL DEFAULT 0,
                    remaining_hours INTEGER NOT NULL DEFAULT 0,
                    year INTEGER NOT NULL,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    UNIQUE(user_id, leave_type, year)
                )");
                DB::statement('INSERT INTO leave_balances_new (id, user_id, leave_type, total_days, used_days, remaining_days, total_hours, used_hours, remaining_hours, year, created_at, updated_at)
                    SELECT id, user_id, leave_type, total_days, used_days, remaining_days, total_hours, used_hours, remaining_hours, year, created_at, updated_at FROM leave_balances');
                Schema::drop('leave_balances');
                Schema::rename('leave_balances_new', 'leave_balances');
                DB::statement('CREATE INDEX leave_balances_user_id_year_index ON leave_balances (user_id, year)');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_balances', function (Blueprint $table) {
            $table->dropColumn(['total_hours', 'used_hours', 'remaining_hours']);
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE leave_balances DROP CONSTRAINT IF EXISTS leave_balances_leave_type_check');
            DB::statement("ALTER TABLE leave_balances ADD CONSTRAINT leave_balances_leave_type_check CHECK (leave_type::text = ANY (ARRAY['sick'::text, 'vacation'::text, 'personal'::text, 'other'::text]))");
        }
    }
};
