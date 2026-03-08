<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveBalance>
 */
class LeaveBalanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalDays = fake()->numberBetween(10, 30);
        $usedDays = fake()->numberBetween(0, min($totalDays, 15));
        $remainingDays = $totalDays - $usedDays;

        $totalHours = fake()->numberBetween(40, 120);
        $usedHours = fake()->numberBetween(0, min($totalHours, 60));
        $remainingHours = $totalHours - $usedHours;

        return [
            'user_id' => User::factory()->employee(),
            'leave_type' => fake()->randomElement(['sick', 'vacation', 'personal', 'other']),
            'total_days' => $totalDays,
            'used_days' => $usedDays,
            'remaining_days' => $remainingDays,
            'total_hours' => $totalHours,
            'used_hours' => $usedHours,
            'remaining_hours' => $remainingHours,
            'year' => fake()->numberBetween(2023, now()->year),
        ];
    }
}
