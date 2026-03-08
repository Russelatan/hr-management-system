<?php

use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->employee = User::factory()->employee()->create();
});

it('shows the leave request list', function () {
    LeaveRequest::factory()->count(2)->create(['user_id' => $this->employee->id]);

    $this->actingAs($this->employee)
        ->get(route('employee.leave.index'))
        ->assertOk()
        ->assertViewIs('employee.leave.index');
});

it('shows the create leave request form', function () {
    $this->actingAs($this->employee)
        ->get(route('employee.leave.create'))
        ->assertOk();
});

it('submits a valid leave request', function () {
    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'personal',
        'start_date' => now()->addDay()->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
        'reason' => 'Family matter.',
    ])->assertRedirect(route('employee.leave.index'));

    $this->assertDatabaseHas('leave_requests', [
        'user_id' => $this->employee->id,
        'leave_type' => 'personal',
        'status' => 'pending',
    ]);
});

it('requires a document for maternity leave', function () {
    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'maternity-leave',
        'start_date' => now()->addDay()->toDateString(),
        'end_date' => now()->addDays(30)->toDateString(),
    ])->assertSessionHasErrors('document');
});

it('accepts a valid document for maternity leave', function () {
    Storage::fake('local');

    $file = UploadedFile::fake()->create('certificate.pdf', 100, 'application/pdf');

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'maternity-leave',
        'start_date' => now()->addDay()->toDateString(),
        'end_date' => now()->addDays(30)->toDateString(),
        'document' => $file,
    ])->assertRedirect(route('employee.leave.index'));

    $request = LeaveRequest::where('user_id', $this->employee->id)->first();
    expect($request->document_path)->not->toBeNull();
});

it('rejects start date in the past', function () {
    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'personal',
        'start_date' => now()->subDay()->toDateString(),
        'end_date' => now()->addDay()->toDateString(),
    ])->assertSessionHasErrors('start_date');
});

it('rejects end date before start date', function () {
    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'personal',
        'start_date' => now()->addDays(5)->toDateString(),
        'end_date' => now()->addDays(3)->toDateString(),
    ])->assertSessionHasErrors('end_date');
});

it('rejects sick leave when hours balance is insufficient', function () {
    LeaveBalance::factory()->create([
        'user_id' => $this->employee->id,
        'leave_type' => 'sick',
        'year' => now()->year,
        'total_hours' => 8,
        'used_hours' => 6,
        'remaining_hours' => 2,
        'total_days' => 10,
        'used_days' => 0,
        'remaining_days' => 10,
    ]);

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'sick',
        'start_date' => now()->addDay()->toDateString(),
        'end_date' => now()->addDay()->toDateString(),
        'hours_requested' => 4,
    ])->assertSessionHas('error');
});

it('shows own leave request detail', function () {
    $leaveRequest = LeaveRequest::factory()->pending()->create(['user_id' => $this->employee->id]);

    $this->actingAs($this->employee)
        ->get(route('employee.leave.show', $leaveRequest))
        ->assertOk();
});

it('cannot view another employee\'s leave request', function () {
    $other = User::factory()->employee()->create();
    $leaveRequest = LeaveRequest::factory()->pending()->create(['user_id' => $other->id]);

    $this->actingAs($this->employee)
        ->get(route('employee.leave.show', $leaveRequest))
        ->assertForbidden();
});

it('validates invalid leave types', function () {
    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'unpaid',
        'start_date' => now()->addDay()->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
    ])->assertSessionHasErrors('leave_type');
});
