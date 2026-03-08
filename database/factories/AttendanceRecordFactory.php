<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceRecord>
 */
class AttendanceRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-3 months', 'now');
        $checkIn = fake()->time('H:i', '09:00');
        $checkOut = fake()->time('H:i', '17:00');
        $status = fake()->randomElement(['present', 'present', 'present', 'late', 'half_day']); // Mostly present

        return [
            'user_id' => User::factory()->employee(),
            'date' => $date,
            'check_in_time' => $checkIn,
            'check_out_time' => $status === 'half_day' ? null : $checkOut,
            'status' => $status,
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the attendance is present.
     */
    public function present(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'present',
            'check_in_time' => fake()->time('H:i', '08:30'),
            'check_out_time' => fake()->time('H:i', '17:30'),
        ]);
    }

    /**
     * Indicate that the attendance is late.
     */
    public function late(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'late',
            'check_in_time' => fake()->time('H:i', '09:30'),
            'check_out_time' => fake()->time('H:i', '17:30'),
        ]);
    }

    /**
     * Indicate that the attendance is absent.
     */
    public function absent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'absent',
            'check_in_time' => null,
            'check_out_time' => null,
        ]);
    }

    /**
     * Indicate that the attendance is a half day.
     */
    public function halfDay(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'half_day',
            'check_in_time' => fake()->time('H:i', '08:30'),
            'check_out_time' => null,
        ]);
    }
}
