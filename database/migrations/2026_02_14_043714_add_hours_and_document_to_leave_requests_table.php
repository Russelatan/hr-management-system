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
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->unsignedInteger('hours_requested')->nullable()->after('days_requested');
            $table->string('document_path')->nullable()->after('reason');
        });

        // Update leave_type to allow new values (PostgreSQL uses CHECK constraint)
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE leave_requests DROP CONSTRAINT IF EXISTS leave_requests_leave_type_check');
            DB::statement("ALTER TABLE leave_requests ADD CONSTRAINT leave_requests_leave_type_check CHECK (leave_type::text = ANY (ARRAY['sick'::text, 'vacation'::text, 'personal'::text, 'other'::text, 'maternity-leave'::text, 'paternity-leave'::text, 'bereavement-leave'::text]))");
        }

        // SQLite: recreate table with new check (SQLite doesn't support DROP CONSTRAINT)
        if ($driver === 'sqlite') {
            DB::transaction(function () {
                DB::statement("CREATE TABLE leave_requests_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    user_id INTEGER NOT NULL,
                    leave_type VARCHAR(50) NOT NULL CHECK(leave_type IN ('sick','vacation','personal','other','maternity-leave','paternity-leave','bereavement-leave')),
                    start_date DATE NOT NULL,
                    end_date DATE NOT NULL,
                    days_requested INTEGER NOT NULL,
                    hours_requested INTEGER NULL,
                    reason TEXT NULL,
                    document_path VARCHAR(255) NULL,
                    status VARCHAR(50) NOT NULL DEFAULT 'pending',
                    approved_by INTEGER NULL,
                    approved_at DATETIME NULL,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
                )");
                DB::statement('INSERT INTO leave_requests_new (id, user_id, leave_type, start_date, end_date, days_requested, hours_requested, reason, document_path, status, approved_by, approved_at, created_at, updated_at)
                    SELECT id, user_id, leave_type, start_date, end_date, days_requested, hours_requested, reason, document_path, status, approved_by, approved_at, created_at, updated_at FROM leave_requests');
                Schema::drop('leave_requests');
                Schema::rename('leave_requests_new', 'leave_requests');
                DB::statement('CREATE INDEX leave_requests_user_id_status_index ON leave_requests (user_id, status)');
                DB::statement('CREATE INDEX leave_requests_status_index ON leave_requests (status)');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['hours_requested', 'document_path']);
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE leave_requests DROP CONSTRAINT IF EXISTS leave_requests_leave_type_check');
            DB::statement("ALTER TABLE leave_requests ADD CONSTRAINT leave_requests_leave_type_check CHECK (leave_type::text = ANY (ARRAY['sick'::text, 'vacation'::text, 'personal'::text, 'other'::text]))");
        }
        // SQLite rollback would need similar table recreation - omit for brevity as down() is rarely used
    }
};
