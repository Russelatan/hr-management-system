<?php

namespace Database\Factories;

use App\Models\LeaveBalance;
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

        return [
            'user_id' => User::factory()->employee(),
            'leave_type' => fake()->randomElement(['sick', 'vacation', 'personal', 'other']),
            'total_days' => $totalDays,
            'used_days' => $usedDays,
            'remaining_days' => $remainingDays,
            'year' => fake()->numberBetween(2023, now()->year),
        ];
    }
}
