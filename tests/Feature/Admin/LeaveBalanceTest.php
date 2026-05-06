<?php

use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->employee = User::factory()->employee()->create();
});

it('lists leave balances', function () {
    foreach (['sick', 'vacation', 'personal'] as $leaveType) {
        LeaveBalance::factory()->create(['user_id' => $this->employee->id, 'year' => now()->year, 'leave_type' => $leaveType]);
    }

    $this->actingAs($this->admin)
        ->get(route('admin.leave-balances.index'))
        ->assertOk()
        ->assertViewIs('admin.leave-balances.index');
});

it('filters leave balances by employee', function () {
    $other = User::factory()->employee()->create();
    LeaveBalance::factory()->create(['user_id' => $this->employee->id, 'year' => now()->year]);
    LeaveBalance::factory()->create(['user_id' => $other->id, 'year' => now()->year]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.leave-balances.index', ['employee_id' => $this->employee->id]));

    $response->assertOk();
    $balances = $response->viewData('leaveBalances');
    expect($balances)->each(fn ($b) => $b->user_id->toBe($this->employee->id));
});

it('shows the edit form for a leave balance', function () {
    $balance = LeaveBalance::factory()->create([
        'user_id' => $this->employee->id,
        'year' => now()->year,
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.leave-balances.edit', $balance))
        ->assertOk()
        ->assertViewIs('admin.leave-balances.edit');
});

it('updates a leave balance and recalculates remaining', function () {
    $balance = LeaveBalance::factory()->create([
        'user_id' => $this->employee->id,
        'leave_type' => 'vacation',
        'year' => now()->year,
        'total_days' => 15,
        'used_days' => 3,
        'remaining_days' => 12,
        'total_hours' => 0,
        'used_hours' => 0,
        'remaining_hours' => 0,
    ]);

    $this->actingAs($this->admin)
        ->put(route('admin.leave-balances.update', $balance), [
            'total_days' => 20,
            'used_days' => 5,
            'total_hours' => 0,
            'used_hours' => 0,
        ])
        ->assertRedirect(route('admin.leave-balances.index'));

    $balance->refresh();
    expect($balance->total_days)->toEqual(20);
    expect($balance->used_days)->toEqual(5);
    expect($balance->remaining_days)->toEqual(15);
});

it('recalculates remaining hours when updating', function () {
    $balance = LeaveBalance::factory()->create([
        'user_id' => $this->employee->id,
        'leave_type' => 'sick',
        'year' => now()->year,
        'total_days' => 10,
        'used_days' => 0,
        'remaining_days' => 10,
        'total_hours' => 80,
        'used_hours' => 8,
        'remaining_hours' => 72,
    ]);

    $this->actingAs($this->admin)
        ->put(route('admin.leave-balances.update', $balance), [
            'total_days' => 10,
            'used_days' => 0,
            'total_hours' => 80,
            'used_hours' => 16,
        ])
        ->assertRedirect(route('admin.leave-balances.index'));

    $balance->refresh();
    expect($balance->remaining_hours)->toEqual(64);
});

it('validates leave balance update inputs', function () {
    $balance = LeaveBalance::factory()->create(['user_id' => $this->employee->id, 'year' => now()->year]);

    $this->actingAs($this->admin)
        ->put(route('admin.leave-balances.update', $balance), [
            'total_days' => -1,
            'used_days' => '',
        ])
        ->assertSessionHasErrors(['total_days', 'used_days']);
});
