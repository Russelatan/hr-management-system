<?php

use App\Models\AttendanceRecord;
use App\Models\LeaveRequest;
use App\Models\PaySlip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the employee dashboard with correct stats', function () {
    $admin = User::factory()->admin()->create();
    $employee = User::factory()->employee()->create();

    PaySlip::factory()->count(2)->create(['user_id' => $employee->id, 'created_by' => $admin->id]);
    LeaveRequest::factory()->pending()->count(1)->create(['user_id' => $employee->id]);
    LeaveRequest::factory()->approved()->count(2)->create(['user_id' => $employee->id]);
    foreach (range(0, 2) as $i) {
        AttendanceRecord::factory()->present()->create([
            'user_id' => $employee->id,
            'date' => now()->startOfMonth()->addDays($i),
        ]);
    }

    $response = $this->actingAs($employee)->get(route('employee.dashboard'));

    $response->assertOk();
    $stats = $response->viewData('stats');

    expect($stats['total_pay_slips'])->toBe(2);
    expect($stats['pending_leave_requests'])->toBe(1);
    expect($stats['approved_leave_requests'])->toBe(2);
});

it('admin cannot access employee dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('employee.dashboard'))->assertForbidden();
});
