<?php

namespace Database\Factories;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-6 months', '+3 months');
        $endDate = fake()->dateTimeBetween($startDate, $startDate->format('Y-m-d') . ' +14 days');
        $daysRequested = (int) $startDate->diff($endDate)->days + 1;

        return [
            'user_id' => User::factory()->employee(),
            'leave_type' => fake()->randomElement(['sick', 'vacation', 'personal', 'other']),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'days_requested' => $daysRequested,
            'reason' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'approved_by' => null,
            'approved_at' => null,
        ];
    }

    /**
     * Indicate that the leave request is approved.
     */
    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
                'approved_by' => User::where('role', 'admin')->first()?->id ?? User::factory()->admin()->create()->id,
                'approved_at' => fake()->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
            ];
        });
    }

    /**
     * Indicate that the leave request is rejected.
     */
    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
                'approved_by' => User::where('role', 'admin')->first()?->id ?? User::factory()->admin()->create()->id,
                'approved_at' => fake()->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
            ];
        });
    }

    /**
     * Indicate that the leave request is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }
}
