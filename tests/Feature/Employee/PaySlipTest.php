<?php

use App\Models\PaySlip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->employee = User::factory()->employee()->create();
});

it('lists own pay slips', function () {
    PaySlip::factory()->count(3)->create(['user_id' => $this->employee->id, 'created_by' => $this->admin->id]);

    $this->actingAs($this->employee)
        ->get(route('employee.pay-slips.index'))
        ->assertOk()
        ->assertViewIs('employee.pay-slips.index');
});

it('shows own pay slip detail', function () {
    $paySlip = PaySlip::factory()->create(['user_id' => $this->employee->id, 'created_by' => $this->admin->id]);

    $this->actingAs($this->employee)
        ->get(route('employee.pay-slips.show', $paySlip))
        ->assertOk();
});

it('cannot view another employee\'s pay slip', function () {
    $other = User::factory()->employee()->create();
    $paySlip = PaySlip::factory()->create(['user_id' => $other->id, 'created_by' => $this->admin->id]);

    $this->actingAs($this->employee)
        ->get(route('employee.pay-slips.show', $paySlip))
        ->assertForbidden();
});

it('download returns 404 when no file exists', function () {
    $paySlip = PaySlip::factory()->create([
        'user_id' => $this->employee->id,
        'created_by' => $this->admin->id,
        'file_path' => null,
    ]);

    $this->actingAs($this->employee)
        ->get(route('employee.pay-slips.download', $paySlip))
        ->assertNotFound();
});
