<?php

namespace Database\Seeders;

use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeaveBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = User::where('role', 'employee')
            ->where('employment_status', 'active')
            ->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No employees found. Skipping leave balance seeding.');
            return;
        }

        $currentYear = now()->year;
        $leaveTypes = ['sick', 'vacation', 'personal'];

        foreach ($employees as $employee) {
            foreach ($leaveTypes as $leaveType) {
                // Skip if balance already exists
                if (LeaveBalance::where('user_id', $employee->id)
                    ->where('leave_type', $leaveType)
                    ->where('year', $currentYear)
                    ->exists()) {
                    continue;
                }

                // Different allocations based on leave type
                $totalDays = match ($leaveType) {
                    'sick' => fake()->numberBetween(10, 15),
                    'vacation' => fake()->numberBetween(15, 25),
                    'personal' => fake()->numberBetween(5, 10),
                    default => 10,
                };

                $usedDays = fake()->numberBetween(0, min($totalDays, 8));
                $remainingDays = $totalDays - $usedDays;

                LeaveBalance::create([
                    'user_id' => $employee->id,
                    'leave_type' => $leaveType,
                    'total_days' => $totalDays,
                    'used_days' => $usedDays,
                    'remaining_days' => $remainingDays,
                    'year' => $currentYear,
                ]);
            }
        }

        $this->command->info('Created leave balances for employees (current year)');
    }
}
