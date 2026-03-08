<?php

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->employee = User::factory()->employee()->create();
});

it('shows own attendance records', function () {
    $dates = collect(range(1, 5))->map(fn ($i) => now()->startOfMonth()->addDays($i - 1)->toDateString());
    foreach ($dates as $date) {
        AttendanceRecord::factory()->present()->create(['user_id' => $this->employee->id, 'date' => $date]);
    }

    $this->actingAs($this->employee)
        ->get(route('employee.attendance.index'))
        ->assertOk()
        ->assertViewIs('employee.attendance.index');
});

it('provides correct attendance stats in a single query', function () {
    $month = now()->startOfMonth();

    AttendanceRecord::factory()->present()->create(['user_id' => $this->employee->id, 'date' => $month->copy()->addDays(0)]);
    AttendanceRecord::factory()->present()->create(['user_id' => $this->employee->id, 'date' => $month->copy()->addDays(1)]);
    AttendanceRecord::factory()->late()->create(['user_id' => $this->employee->id, 'date' => $month->copy()->addDays(2)]);
    AttendanceRecord::factory()->absent()->create(['user_id' => $this->employee->id, 'date' => $month->copy()->addDays(3)]);

    $response = $this->actingAs($this->employee)
        ->get(route('employee.attendance.index', [
            'start_date' => $month->toDateString(),
            'end_date' => $month->copy()->endOfMonth()->toDateString(),
        ]));

    $stats = $response->viewData('stats');

    expect($stats['total_days'])->toBe(4);
    expect($stats['present_days'])->toBe(2);
    expect($stats['late_days'])->toBe(1);
    expect($stats['absent_days'])->toBe(1);
});

it('defaults to the current month', function () {
    $this->actingAs($this->employee)
        ->get(route('employee.attendance.index'))
        ->assertOk()
        ->assertViewHas('startDate', now()->startOfMonth()->format('Y-m-d'))
        ->assertViewHas('endDate', now()->endOfMonth()->format('Y-m-d'));
});
