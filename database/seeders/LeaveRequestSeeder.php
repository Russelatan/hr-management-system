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
                $daysRequested = (int) $startDate->diff($endDate)->days + 1;

                $status = fake()->randomElement(['pending', 'approved', 'rejected']);
                $approvedBy = null;
                $approvedAt = null;

                if ($status !== 'pending') {
                    $approvedBy = $admin->id;
                    $approvedAt = fake()->dateTimeBetween($startDate, 'now');
                }

                LeaveRequest::create([
                    'user_id' => $employee->id,
                    'leave_type' => fake()->randomElement(['sick', 'vacation', 'personal', 'other']),
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'days_requested' => $daysRequested,
                    'reason' => fake()->optional(0.8)->sentence(),
                    'status' => $status,
                    'approved_by' => $approvedBy,
                    'approved_at' => $approvedAt,
                ]);
            }
        }

        $this->command->info('Created leave requests for employees');
    }
}
