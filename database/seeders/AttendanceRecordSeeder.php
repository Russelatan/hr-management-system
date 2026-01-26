<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AttendanceRecordSeeder extends Seeder
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
            $this->command->warn('No employees found. Skipping attendance record seeding.');
            return;
        }

        // Create attendance records for the last 3 months (excluding weekends)
        foreach ($employees as $employee) {
            $startDate = Carbon::now()->subMonths(3)->startOfMonth();
            $endDate = Carbon::now();

            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                // Skip weekends
                if ($currentDate->isWeekend()) {
                    $currentDate->addDay();
                    continue;
                }

                // Skip if record already exists
                if (AttendanceRecord::where('user_id', $employee->id)
                    ->where('date', $currentDate->format('Y-m-d'))
                    ->exists()) {
                    $currentDate->addDay();
                    continue;
                }

                // 90% present, 5% late, 3% half_day, 2% absent
                $rand = fake()->numberBetween(1, 100);
                
                if ($rand <= 90) {
                    // Present
                    $checkIn = fake()->time('H:i', '08:30');
                    $checkOut = fake()->time('H:i', '17:30');
                    $status = 'present';
                } elseif ($rand <= 95) {
                    // Late
                    $checkIn = fake()->time('H:i', '09:30');
                    $checkOut = fake()->time('H:i', '17:30');
                    $status = 'late';
                } elseif ($rand <= 98) {
                    // Half day
                    $checkIn = fake()->time('H:i', '08:30');
                    $checkOut = null;
                    $status = 'half_day';
                } else {
                    // Absent
                    $checkIn = null;
                    $checkOut = null;
                    $status = 'absent';
                }

                AttendanceRecord::create([
                    'user_id' => $employee->id,
                    'date' => $currentDate->format('Y-m-d'),
                    'check_in_time' => $checkIn,
                    'check_out_time' => $checkOut,
                    'status' => $status,
                    'notes' => fake()->optional(0.2)->sentence(),
                ]);

                $currentDate->addDay();
            }
        }

        $this->command->info('Created attendance records for employees (last 3 months, weekdays only)');
    }
}
