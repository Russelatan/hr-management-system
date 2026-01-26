<?php

namespace Database\Factories;

use App\Models\PaySlip;
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
        $grossSalary = fake()->randomFloat(2, 30000, 150000);
        $deductions = fake()->randomFloat(2, 5000, 20000);
        $netSalary = $grossSalary - $deductions;

        return [
            'user_id' => User::factory()->employee(),
            'month' => fake()->numberBetween(1, 12),
            'year' => fake()->numberBetween(2023, now()->year),
            'gross_salary' => $grossSalary,
            'deductions' => $deductions,
            'net_salary' => $netSalary,
            'file_path' => null, // No file in seeders
            'distributed_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'created_by' => User::factory()->admin(),
        ];
    }
}
