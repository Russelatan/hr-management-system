<?php

namespace Database\Seeders;

use App\Models\PaySlip;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaySlipSeeder extends Seeder
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
            $this->command->warn('No employees found. Skipping pay slip seeding.');
            return;
        }

        // Create pay slips for the last 6 months for each employee
        foreach ($employees as $employee) {
            $currentYear = now()->year;
            $currentMonth = now()->month;

            for ($i = 0; $i < 6; $i++) {
                $month = $currentMonth - $i;
                $year = $currentYear;

                if ($month <= 0) {
                    $month += 12;
                    $year--;
                }

                // Skip if pay slip already exists
                if (PaySlip::where('user_id', $employee->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->exists()) {
                    continue;
                }

                $grossSalary = fake()->randomFloat(2, 40000, 120000);
                $deductions = fake()->randomFloat(2, 5000, 15000);
                $netSalary = $grossSalary - $deductions;

                PaySlip::create([
                    'user_id' => $employee->id,
                    'month' => $month,
                    'year' => $year,
                    'gross_salary' => $grossSalary,
                    'deductions' => $deductions,
                    'net_salary' => $netSalary,
                    'file_path' => null,
                    'distributed_at' => now()->subMonths($i),
                    'created_by' => $admin->id,
                ]);
            }
        }

        $this->command->info('Created pay slips for employees (last 6 months)');
    }
}
