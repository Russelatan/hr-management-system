<?php

use App\Models\AttendanceRecord;
use App\Models\User;
use App\Services\PaySlipComputationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new PaySlipComputationService;

    $this->employee = User::factory()->employee()->create([
        'basic_salary' => 50000.00,
        'sss_contribution' => 1125.00,
        'philhealth_contribution' => 625.00,
        'pagibig_contribution' => 100.00,
        'other_deductions' => 0.00,
        'working_days_per_month' => 22,
    ]);
});

it('computes net salary with no absences', function () {
    $result = $this->service->compute($this->employee, 3, 2026);

    expect($result['basic_salary'])->toBe(50000.0)
        ->and($result['absent_days'])->toBe(0)
        ->and($result['absent_deduction'])->toBe(0.0)
        ->and($result['gross_salary'])->toBe(50000.0)
        ->and($result['sss_contribution'])->toBe(1125.0)
        ->and($result['philhealth_contribution'])->toBe(625.0)
        ->and($result['pagibig_contribution'])->toBe(100.0)
        ->and($result['total_deductions'])->toBe(1850.0)
        ->and($result['net_salary'])->toBe(48150.0);
});

it('deducts absent days from gross salary', function () {
    AttendanceRecord::factory()->create([
        'user_id' => $this->employee->id,
        'date' => '2026-03-05',
        'status' => 'absent',
    ]);

    AttendanceRecord::factory()->create([
        'user_id' => $this->employee->id,
        'date' => '2026-03-06',
        'status' => 'absent',
    ]);

    $result = $this->service->compute($this->employee, 3, 2026);

    // Use the same rounding approach as the service: round(absents * (salary/days), 2)
    $expectedAbsentDeduction = round(2 * (50000 / 22), 2);
    $expectedGross = round(50000 - $expectedAbsentDeduction, 2);
    $expectedNet = round($expectedGross - 1850, 2);

    expect($result['absent_days'])->toBe(2)
        ->and($result['absent_deduction'])->toBe($expectedAbsentDeduction)
        ->and($result['gross_salary'])->toBe($expectedGross)
        ->and($result['net_salary'])->toBe($expectedNet);
});

it('does not count present or late statuses as absent or half_day', function () {
    AttendanceRecord::factory()->create(['user_id' => $this->employee->id, 'date' => '2026-03-01', 'status' => 'present']);
    AttendanceRecord::factory()->create(['user_id' => $this->employee->id, 'date' => '2026-03-02', 'status' => 'late']);

    $result = $this->service->compute($this->employee, 3, 2026);

    expect($result['absent_days'])->toBe(0)
        ->and($result['half_days'])->toBe(0)
        ->and($result['gross_salary'])->toBe(50000.0);
});

it('only counts attendance records within the requested month', function () {
    AttendanceRecord::factory()->create([
        'user_id' => $this->employee->id,
        'date' => '2026-02-15',
        'status' => 'absent',
    ]);

    $result = $this->service->compute($this->employee, 3, 2026);

    expect($result['absent_days'])->toBe(0);
});

it('includes other deductions in total deductions', function () {
    $this->employee->update(['other_deductions' => 500.00]);

    $result = $this->service->compute($this->employee, 3, 2026);

    expect($result['other_deductions'])->toBe(500.0)
        ->and($result['total_deductions'])->toBe(2350.0)
        ->and($result['net_salary'])->toBe(47650.0);
});

it('returns correct computation keys', function () {
    $result = $this->service->compute($this->employee, 3, 2026);

    expect($result)->toHaveKeys([
        'basic_salary',
        'working_days_per_month',
        'daily_rate',
        'total_attendance_records',
        'absent_days',
        'absent_deduction',
        'half_days',
        'half_day_deduction',
        'gross_salary',
        'sss_contribution',
        'philhealth_contribution',
        'pagibig_contribution',
        'other_deductions',
        'total_deductions',
        'net_salary',
    ]);
});

it('deducts 0.5 x daily rate for each half_day record', function () {
    AttendanceRecord::factory()->create([
        'user_id' => $this->employee->id,
        'date' => '2026-03-05',
        'status' => 'half_day',
    ]);

    AttendanceRecord::factory()->create([
        'user_id' => $this->employee->id,
        'date' => '2026-03-06',
        'status' => 'half_day',
    ]);

    $result = $this->service->compute($this->employee, 3, 2026);

    $expectedHalfDayDeduction = round(2 * (50000 / 22) * 0.5, 2);
    $expectedGross = round(50000 - $expectedHalfDayDeduction, 2);

    expect($result['half_days'])->toBe(2)
        ->and($result['half_day_deduction'])->toBe($expectedHalfDayDeduction)
        ->and($result['gross_salary'])->toBe($expectedGross);
});

it('deducts both absent and half_day records independently', function () {
    AttendanceRecord::factory()->create([
        'user_id' => $this->employee->id,
        'date' => '2026-03-05',
        'status' => 'absent',
    ]);

    AttendanceRecord::factory()->create([
        'user_id' => $this->employee->id,
        'date' => '2026-03-06',
        'status' => 'half_day',
    ]);

    $result = $this->service->compute($this->employee, 3, 2026);

    $dailyRate = 50000 / 22;
    $expectedAbsentDeduction = round(1 * $dailyRate, 2);
    $expectedHalfDayDeduction = round(1 * $dailyRate * 0.5, 2);
    $expectedGross = round(50000 - $expectedAbsentDeduction - $expectedHalfDayDeduction, 2);

    expect($result['absent_days'])->toBe(1)
        ->and($result['half_days'])->toBe(1)
        ->and($result['gross_salary'])->toBe($expectedGross);
});

it('net salary is never negative', function () {
    $this->employee->update([
        'basic_salary' => 100.00,
        'sss_contribution' => 500.00,
        'philhealth_contribution' => 500.00,
        'pagibig_contribution' => 500.00,
    ]);

    AttendanceRecord::factory()->create([
        'user_id' => $this->employee->id,
        'date' => '2026-03-05',
        'status' => 'absent',
    ]);

    $result = $this->service->compute($this->employee, 3, 2026);

    expect($result['net_salary'])->toBeGreaterThanOrEqual(0);
});
