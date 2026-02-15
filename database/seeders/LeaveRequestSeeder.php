<?php

namespace Database\Seeders;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeaveRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $employees = User::where('role', 'employee')
            ->where('employment_status', 'active')
            ->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No employees found. Skipping leave request seeding.');
            return;
        }

        // Create 2-4 leave requests per employee
        foreach ($employees as $employee) {
            $requestCount = fake()->numberBetween(2, 4);

            for ($i = 0; $i < $requestCount; $i++) {
                $startDate = fake()->dateTimeBetween('-6 months', '+2 months');
                $endDate = fake()->dateTimeBetween($startDate, $startDate->format('Y-m-d') . ' +10 days');
                $leaveType = fake()->randomElement(['sick', 'vacation', 'personal', 'maternity-leave', 'paternity-leave', 'bereavement-leave', 'other']);
                $daysRequested = (int) $startDate->diff($endDate)->days + 1;
                $hoursRequested = in_array($leaveType, LeaveRequest::leaveTypesWithHoursSupport()) && $daysRequested === 1 && fake()->boolean(30)
                    ? fake()->numberBetween(1, 8)
                    : null;
                if ($hoursRequested) {
                    $daysRequested = 0;
                }

                $status = fake()->randomElement(['pending', 'approved', 'rejected']);
                $approvedBy = null;
                $approvedAt = null;

                if ($status !== 'pending') {
                    $approvedBy = $admin->id;
                    $approvedAt = $startDate->getTimestamp() <= time()
                        ? fake()->dateTimeBetween($startDate->format('Y-m-d'), 'now')
                        : now();
                }

                LeaveRequest::create([
                    'user_id' => $employee->id,
                    'leave_type' => $leaveType,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'days_requested' => $daysRequested,
                    'hours_requested' => $hoursRequested,
                    'reason' => fake()->optional(0.8)->sentence(),
                    'document_path' => null,
                    'status' => $status,
                    'approved_by' => $approvedBy,
                    'approved_at' => $approvedAt,
                ]);
            }
        }

        $this->command->info('Created leave requests for employees');
    }
}
