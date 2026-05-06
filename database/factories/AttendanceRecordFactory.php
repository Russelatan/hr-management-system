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
        $status = fake()->randomElement(['present', 'present', 'present', 'late', 'half_day']);

        return [
            'user_id' => User::factory()->employee(),
            'date' => $date,
            'morning_in' => '08:00',
            'morning_out' => '12:00',
            'afternoon_in' => $status === 'half_day' ? null : '13:00',
            'afternoon_out' => $status === 'half_day' ? null : '17:00',
            'status' => $status,
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the attendance is present (full day).
     */
    public function present(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'present',
            'morning_in' => '08:00',
            'morning_out' => '12:00',
            'afternoon_in' => '13:00',
            'afternoon_out' => '17:00',
        ]);
    }

    /**
     * Indicate that the attendance is late (full day but late morning in).
     */
    public function late(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'late',
            'morning_in' => '09:30',
            'morning_out' => '12:00',
            'afternoon_in' => '13:00',
            'afternoon_out' => '17:00',
        ]);
    }

    /**
     * Indicate that the attendance is absent (no sessions recorded).
     */
    public function absent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'absent',
            'morning_in' => null,
            'morning_out' => null,
            'afternoon_in' => null,
            'afternoon_out' => null,
        ]);
    }

    /**
     * Indicate that the attendance is a half day (morning session only).
     */
    public function halfDay(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'half_day',
            'morning_in' => '08:00',
            'morning_out' => '12:00',
            'afternoon_in' => null,
            'afternoon_out' => null,
        ]);
    }
}
