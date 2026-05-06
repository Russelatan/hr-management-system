<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaySlip>
 */
class PaySlipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $basicSalary = 50000.00;
        $sss = 1125.00;
        $philhealth = 625.00;
        $pagibig = 100.00;
        $otherDeductions = 0.00;
        $totalDeductions = $sss + $philhealth + $pagibig + $otherDeductions;
        $grossSalary = $basicSalary;
        $netSalary = $grossSalary - $totalDeductions;

        return [
            'user_id' => User::factory()->employee(),
            'month' => fake()->numberBetween(1, 12),
            'year' => fake()->numberBetween(2023, now()->year),
            'gross_salary' => $grossSalary,
            'deductions' => $totalDeductions,
            'net_salary' => $netSalary,
            'file_path' => null,
            'distributed_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'created_by' => User::factory()->admin(),
            'computation_notes' => [
                'basic_salary' => $basicSalary,
                'working_days_per_month' => 22,
                'daily_rate' => round($basicSalary / 22, 2),
                'total_attendance_records' => 0,
                'absent_days' => 0,
                'absent_deduction' => 0.00,
                'gross_salary' => $grossSalary,
                'sss_contribution' => $sss,
                'philhealth_contribution' => $philhealth,
                'pagibig_contribution' => $pagibig,
                'other_deductions' => $otherDeductions,
                'total_deductions' => $totalDeductions,
                'net_salary' => $netSalary,
            ],
        ];
    }
}
