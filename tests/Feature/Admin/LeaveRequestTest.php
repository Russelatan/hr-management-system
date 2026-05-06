<?php

use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->employee = User::factory()->employee()->create();
});

it('lists leave requests', function () {
    LeaveRequest::factory()->count(3)->create(['user_id' => $this->employee->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.leave-requests.index'))
        ->assertOk()
        ->assertViewIs('admin.leave-requests.index');
});

it('filters leave requests by status', function () {
    LeaveRequest::factory()->pending()->create(['user_id' => $this->employee->id]);
    LeaveRequest::factory()->approved()->create(['user_id' => $this->employee->id]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.leave-requests.index', ['status' => 'pending']));

    $response->assertOk();
    $leaveRequests = $response->viewData('leaveRequests');
    expect($leaveRequests)->each(fn ($lr) => $lr->status->toBe('pending'));
});

it('shows a leave request detail', function () {
    $leaveRequest = LeaveRequest::factory()->pending()->create(['user_id' => $this->employee->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.leave-requests.show', $leaveRequest))
        ->assertOk();
});

it('approves a pending leave request and updates day balance', function () {
    $startDate = Carbon::now()->next('Monday');

    $leaveRequest = LeaveRequest::factory()->pending()->create([
        'user_id' => $this->employee->id,
        'leave_type' => 'personal',
        'start_date' => $startDate,
        'end_date' => $startDate,
        'days_requested' => 2,
        'hours_requested' => null,
    ]);

    LeaveBalance::factory()->create([
        'user_id' => $this->employee->id,
        'leave_type' => 'personal',
        'year' => $startDate->year,
        'total_days' => 10,
        'used_days' => 0,
        'remaining_days' => 10,
        'total_hours' => 0,
        'used_hours' => 0,
        'remaining_hours' => 0,
    ]);

    $this->actingAs($this->admin)
        ->post(route('admin.leave-requests.approve', $leaveRequest))
        ->assertRedirect(route('admin.leave-requests.index'));

    $leaveRequest->refresh();
    expect($leaveRequest->status)->toBe('approved');
    expect($leaveRequest->approved_by)->toBe($this->admin->id);

    $balance = LeaveBalance::where('user_id', $this->employee->id)
        ->where('leave_type', 'personal')
        ->where('year', $startDate->year)
        ->first();

    expect($balance->used_days)->toBe(2);
    expect($balance->remaining_days)->toBe(8);
});

it('approves a partial-day leave and updates hour balance', function () {
    $startDate = Carbon::now()->next('Monday');

    $leaveRequest = LeaveRequest::factory()->pending()->create([
        'user_id' => $this->employee->id,
        'leave_type' => 'sick',
        'start_date' => $startDate,
        'end_date' => $startDate,
        'days_requested' => 0,
        'hours_requested' => 4,
    ]);

    LeaveBalance::factory()->create([
        'user_id' => $this->employee->id,
        'leave_type' => 'sick',
        'year' => $startDate->year,
        'total_days' => 10,
        'used_days' => 0,
        'remaining_days' => 10,
        'total_hours' => 80,
        'used_hours' => 0,
        'remaining_hours' => 80,
    ]);

    $this->actingAs($this->admin)
        ->post(route('admin.leave-requests.approve', $leaveRequest))
        ->assertRedirect();

    $balance = LeaveBalance::where('user_id', $this->employee->id)
        ->where('leave_type', 'sick')
        ->where('year', $startDate->year)
        ->first();

    expect($balance->used_hours)->toBe(4);
    expect($balance->remaining_hours)->toBe(76);
    expect($balance->used_days)->toBe(0);
});

it('cannot approve when no balance record exists', function () {
    $leaveRequest = LeaveRequest::factory()->pending()->create([
        'user_id' => $this->employee->id,
        'leave_type' => 'vacation',
        'days_requested' => 3,
        'hours_requested' => null,
    ]);

    $this->actingAs($this->admin)
        ->post(route('admin.leave-requests.approve', $leaveRequest))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($leaveRequest->fresh()->status)->toBe('pending');
});

it('cannot approve when day balance is insufficient', function () {
    $startDate = Carbon::now()->next('Monday');

    $leaveRequest = LeaveRequest::factory()->pending()->create([
        'user_id' => $this->employee->id,
        'leave_type' => 'vacation',
        'start_date' => $startDate,
        'end_date' => $startDate,
        'days_requested' => 5,
        'hours_requested' => null,
    ]);

    LeaveBalance::factory()->create([
        'user_id' => $this->employee->id,
        'leave_type' => 'vacation',
        'year' => $startDate->year,
        'total_days' => 5,
        'used_days' => 4,
        'remaining_days' => 1,
        'total_hours' => 0,
        'used_hours' => 0,
        'remaining_hours' => 0,
    ]);

    $this->actingAs($this->admin)
        ->post(route('admin.leave-requests.approve', $leaveRequest))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($leaveRequest->fresh()->status)->toBe('pending');
});

it('cannot approve when hour balance is insufficient', function () {
    $startDate = Carbon::now()->next('Monday');

    $leaveRequest = LeaveRequest::factory()->pending()->create([
        'user_id' => $this->employee->id,
        'leave_type' => 'sick',
        'start_date' => $startDate,
        'end_date' => $startDate,
        'days_requested' => 0,
        'hours_requested' => 8,
    ]);

    LeaveBalance::factory()->create([
        'user_id' => $this->employee->id,
        'leave_type' => 'sick',
        'year' => $startDate->year,
        'total_days' => 10,
        'used_days' => 0,
        'remaining_days' => 10,
        'total_hours' => 8,
        'used_hours' => 6,
        'remaining_hours' => 2,
    ]);

    $this->actingAs($this->admin)
        ->post(route('admin.leave-requests.approve', $leaveRequest))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($leaveRequest->fresh()->status)->toBe('pending');
});

it('uses start_date year for balance lookup when approving', function () {
    $nextYear = now()->year + 1;
    $startDate = Carbon::create($nextYear, 1, 5);

    $leaveRequest = LeaveRequest::factory()->pending()->create([
        'user_id' => $this->employee->id,
        'leave_type' => 'personal',
        'start_date' => $startDate,
        'end_date' => $startDate->copy()->addDays(2),
        'days_requested' => 3,
        'hours_requested' => null,
    ]);

    // Balance for next year only
    LeaveBalance::factory()->create([
        'user_id' => $this->employee->id,
        'leave_type' => 'personal',
        'year' => $nextYear,
        'total_days' => 15,
        'used_days' => 0,
        'remaining_days' => 15,
        'total_hours' => 0,
        'used_hours' => 0,
        'remaining_hours' => 0,
    ]);

    $this->actingAs($this->admin)
        ->post(route('admin.leave-requests.approve', $leaveRequest))
        ->assertRedirect(route('admin.leave-requests.index'));

    expect($leaveRequest->fresh()->status)->toBe('approved');
});

it('cannot approve an already-processed leave request', function () {
    $leaveRequest = LeaveRequest::factory()->approved()->create(['user_id' => $this->employee->id]);

    $this->actingAs($this->admin)
        ->post(route('admin.leave-requests.approve', $leaveRequest))
        ->assertSessionHas('error');
});

it('rejects a pending leave request', function () {
    $leaveRequest = LeaveRequest::factory()->pending()->create(['user_id' => $this->employee->id]);

    $this->actingAs($this->admin)
        ->post(route('admin.leave-requests.reject', $leaveRequest))
        ->assertRedirect(route('admin.leave-requests.index'));

    expect($leaveRequest->fresh()->status)->toBe('rejected');
});

it('cannot reject an already-processed leave request', function () {
    $leaveRequest = LeaveRequest::factory()->rejected()->create(['user_id' => $this->employee->id]);

    $this->actingAs($this->admin)
        ->post(route('admin.leave-requests.reject', $leaveRequest))
        ->assertSessionHas('error');
});
