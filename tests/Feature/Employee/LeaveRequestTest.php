<?php

use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
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
    $monday = Carbon::now()->next('Monday');

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'personal',
        'start_date' => $monday->toDateString(),
        'end_date' => $monday->copy()->addDay()->toDateString(),
        'reason' => 'Family matter.',
    ])->assertRedirect(route('employee.leave.index'));

    $this->assertDatabaseHas('leave_requests', [
        'user_id' => $this->employee->id,
        'leave_type' => 'personal',
        'status' => 'pending',
    ]);
});

it('allows submitting a leave request without a reason', function () {
    $monday = Carbon::now()->next('Monday');

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'personal',
        'start_date' => $monday->toDateString(),
        'end_date' => $monday->toDateString(),
    ])->assertRedirect(route('employee.leave.index'));

    $this->assertDatabaseHas('leave_requests', [
        'user_id' => $this->employee->id,
        'reason' => null,
    ]);
});

it('excludes weekends from days_requested', function () {
    // Saturday to Sunday = 0 weekdays
    $saturday = Carbon::now()->next('Saturday');

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'personal',
        'start_date' => $saturday->toDateString(),
        'end_date' => $saturday->copy()->addDay()->toDateString(), // Sunday
    ])->assertRedirect(route('employee.leave.index'));

    $request = LeaveRequest::where('user_id', $this->employee->id)->first();
    expect($request->days_requested)->toBe(0);
});

it('counts only weekdays when spanning a weekend', function () {
    // Friday to Monday = 2 weekdays (Friday + Monday)
    $friday = Carbon::now()->next('Friday');
    $monday = $friday->copy()->addDays(3); // next Monday

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'personal',
        'start_date' => $friday->toDateString(),
        'end_date' => $monday->toDateString(),
    ])->assertRedirect(route('employee.leave.index'));

    $request = LeaveRequest::where('user_id', $this->employee->id)->first();
    expect($request->days_requested)->toBe(2);
});

it('blocks overlapping leave requests', function () {
    $monday = Carbon::now()->next('Monday');

    LeaveRequest::factory()->pending()->create([
        'user_id' => $this->employee->id,
        'start_date' => $monday->toDateString(),
        'end_date' => $monday->copy()->addDays(4)->toDateString(),
    ]);

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'personal',
        'start_date' => $monday->copy()->addDays(2)->toDateString(),
        'end_date' => $monday->copy()->addDays(2)->toDateString(),
    ])->assertSessionHas('error');
});

it('allows non-overlapping leave requests', function () {
    $monday = Carbon::now()->next('Monday');

    LeaveRequest::factory()->pending()->create([
        'user_id' => $this->employee->id,
        'start_date' => $monday->toDateString(),
        'end_date' => $monday->copy()->addDays(2)->toDateString(),
    ]);

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'personal',
        'start_date' => $monday->copy()->addDays(7)->toDateString(),
        'end_date' => $monday->copy()->addDays(7)->toDateString(),
    ])->assertRedirect(route('employee.leave.index'));
});

it('checks balance for all leave types with a balance record', function () {
    $monday = Carbon::now()->next('Monday');

    LeaveBalance::factory()->create([
        'user_id' => $this->employee->id,
        'leave_type' => 'personal',
        'year' => $monday->year,
        'total_days' => 3,
        'used_days' => 3,
        'remaining_days' => 0,
        'total_hours' => 0,
        'used_hours' => 0,
        'remaining_hours' => 0,
    ]);

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'personal',
        'start_date' => $monday->toDateString(),
        'end_date' => $monday->toDateString(),
    ])->assertSessionHas('error');
});

it('allows leave types with no balance record to pass unchecked', function () {
    $monday = Carbon::now()->next('Monday');

    // No balance record for bereavement-leave - but has a document
    Storage::fake('local');
    $file = UploadedFile::fake()->create('cert.pdf', 50, 'application/pdf');

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'bereavement-leave',
        'start_date' => $monday->toDateString(),
        'end_date' => $monday->copy()->addDays(2)->toDateString(),
        'document' => $file,
    ])->assertRedirect(route('employee.leave.index'));
});

it('uses start_date year for balance lookup', function () {
    $nextYear = now()->year + 1;
    $monday = Carbon::create($nextYear, 1, 6); // First Monday of next year approx

    // Balance for current year - should NOT be used
    LeaveBalance::factory()->create([
        'user_id' => $this->employee->id,
        'leave_type' => 'personal',
        'year' => now()->year,
        'total_days' => 0,
        'used_days' => 0,
        'remaining_days' => 0,
        'total_hours' => 0,
        'used_hours' => 0,
        'remaining_hours' => 0,
    ]);

    // Balance for next year - should be used
    LeaveBalance::factory()->create([
        'user_id' => $this->employee->id,
        'leave_type' => 'personal',
        'year' => $nextYear,
        'total_days' => 10,
        'used_days' => 0,
        'remaining_days' => 10,
        'total_hours' => 0,
        'used_hours' => 0,
        'remaining_hours' => 0,
    ]);

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'personal',
        'start_date' => $monday->toDateString(),
        'end_date' => $monday->toDateString(),
    ])->assertRedirect(route('employee.leave.index'));
});

it('rejects sick leave when hours balance is insufficient', function () {
    $monday = Carbon::now()->next('Monday');

    LeaveBalance::factory()->create([
        'user_id' => $this->employee->id,
        'leave_type' => 'sick',
        'year' => $monday->year,
        'total_hours' => 8,
        'used_hours' => 6,
        'remaining_hours' => 2,
        'total_days' => 10,
        'used_days' => 0,
        'remaining_days' => 10,
    ]);

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'sick',
        'start_date' => $monday->toDateString(),
        'end_date' => $monday->toDateString(),
        'hours_requested' => 4,
    ])->assertSessionHas('error');
});

it('requires a document for maternity leave', function () {
    $monday = Carbon::now()->next('Monday');

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'maternity-leave',
        'start_date' => $monday->toDateString(),
        'end_date' => $monday->copy()->addDays(30)->toDateString(),
    ])->assertSessionHasErrors('document');
});

it('requires a document for paternity leave', function () {
    $monday = Carbon::now()->next('Monday');

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'paternity-leave',
        'start_date' => $monday->toDateString(),
        'end_date' => $monday->copy()->addDays(7)->toDateString(),
    ])->assertSessionHasErrors('document');
});

it('requires a document for bereavement leave', function () {
    $monday = Carbon::now()->next('Monday');

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'bereavement-leave',
        'start_date' => $monday->toDateString(),
        'end_date' => $monday->copy()->addDays(3)->toDateString(),
    ])->assertSessionHasErrors('document');
});

it('accepts a valid document for maternity leave', function () {
    Storage::fake('local');
    $monday = Carbon::now()->next('Monday');
    $file = UploadedFile::fake()->create('certificate.pdf', 100, 'application/pdf');

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'maternity-leave',
        'start_date' => $monday->toDateString(),
        'end_date' => $monday->copy()->addDays(30)->toDateString(),
        'document' => $file,
    ])->assertRedirect(route('employee.leave.index'));

    $request = LeaveRequest::where('user_id', $this->employee->id)->first();
    expect($request->document_path)->not->toBeNull();
});

it('blocks same-day leave submitted at or after 08:00 AM', function () {
    Carbon::setTestNow(now()->setTime(9, 0));

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'personal',
        'start_date' => now()->toDateString(),
        'end_date' => now()->toDateString(),
    ])->assertSessionHasErrors('start_date');

    Carbon::setTestNow();
});

it('allows same-day leave before 08:00 AM', function () {
    Carbon::setTestNow(now()->setTime(7, 30));

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'personal',
        'start_date' => now()->toDateString(),
        'end_date' => now()->toDateString(),
    ])->assertRedirect(route('employee.leave.index'));

    Carbon::setTestNow();
});

it('rejects start date in the past', function () {
    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'personal',
        'start_date' => now()->subDay()->toDateString(),
        'end_date' => now()->addDay()->toDateString(),
    ])->assertSessionHasErrors('start_date');
});

it('rejects end date before start date', function () {
    $monday = Carbon::now()->next('Monday');

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'personal',
        'start_date' => $monday->copy()->addDays(5)->toDateString(),
        'end_date' => $monday->copy()->addDays(3)->toDateString(),
    ])->assertSessionHasErrors('end_date');
});

it('cancels a pending leave request', function () {
    $leaveRequest = LeaveRequest::factory()->pending()->create(['user_id' => $this->employee->id]);

    $this->actingAs($this->employee)
        ->post(route('employee.leave.cancel', $leaveRequest))
        ->assertRedirect(route('employee.leave.index'));

    expect($leaveRequest->fresh()->status)->toBe('cancelled');
});

it('cannot cancel a non-pending leave request', function () {
    $leaveRequest = LeaveRequest::factory()->approved()->create(['user_id' => $this->employee->id]);

    $this->actingAs($this->employee)
        ->post(route('employee.leave.cancel', $leaveRequest))
        ->assertStatus(422);
});

it('cannot cancel another employee\'s leave request', function () {
    $other = User::factory()->employee()->create();
    $leaveRequest = LeaveRequest::factory()->pending()->create(['user_id' => $other->id]);

    $this->actingAs($this->employee)
        ->post(route('employee.leave.cancel', $leaveRequest))
        ->assertForbidden();
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
    $monday = Carbon::now()->next('Monday');

    $this->actingAs($this->employee)->post(route('employee.leave.store'), [
        'leave_type' => 'unpaid',
        'start_date' => $monday->toDateString(),
        'end_date' => $monday->copy()->addDays(2)->toDateString(),
    ])->assertSessionHasErrors('leave_type');
});
