<?php

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->employee = User::factory()->employee()->create();
});

it('lists attendance records', function () {
    AttendanceRecord::factory()->count(3)->create(['user_id' => $this->employee->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.attendance.index'))
        ->assertOk()
        ->assertViewIs('admin.attendance.index');
});

it('shows the create attendance form', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.attendance.create'))
        ->assertOk();
});

it('creates an attendance record', function () {
    $this->actingAs($this->admin)->post(route('admin.attendance.store'), [
        'user_id' => $this->employee->id,
        'date' => '2026-03-08',
        'check_in_time' => '08:30',
        'check_out_time' => '17:30',
        'status' => 'present',
    ])->assertRedirect(route('admin.attendance.index'));

    $this->assertDatabaseHas('attendance_records', [
        'user_id' => $this->employee->id,
        'date' => '2026-03-08',
        'status' => 'present',
    ]);
});

it('upserts on duplicate user+date', function () {
    AttendanceRecord::factory()->create([
        'user_id' => $this->employee->id,
        'date' => '2026-03-08',
        'status' => 'present',
    ]);

    $this->actingAs($this->admin)->post(route('admin.attendance.store'), [
        'user_id' => $this->employee->id,
        'date' => '2026-03-08',
        'status' => 'late',
    ]);

    expect(AttendanceRecord::where('user_id', $this->employee->id)->where('date', '2026-03-08')->count())->toBe(1);
    expect(AttendanceRecord::where('user_id', $this->employee->id)->where('date', '2026-03-08')->first()->status)->toBe('late');
});

it('rejects an invalid status', function () {
    $this->actingAs($this->admin)->post(route('admin.attendance.store'), [
        'user_id' => $this->employee->id,
        'date' => '2026-03-08',
        'status' => 'sick',
    ])->assertSessionHasErrors('status');
});

it('shows the edit attendance form', function () {
    $record = AttendanceRecord::factory()->create(['user_id' => $this->employee->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.attendance.edit', $record))
        ->assertOk();
});

it('updates an attendance record', function () {
    $record = AttendanceRecord::factory()->present()->create(['user_id' => $this->employee->id]);

    $this->actingAs($this->admin)->put(route('admin.attendance.update', $record), [
        'status' => 'late',
        'check_in_time' => '09:15',
        'check_out_time' => '17:30',
    ])->assertRedirect(route('admin.attendance.index'));

    expect($record->fresh()->status)->toBe('late');
});

it('deletes an attendance record', function () {
    $record = AttendanceRecord::factory()->create(['user_id' => $this->employee->id]);

    $this->actingAs($this->admin)
        ->delete(route('admin.attendance.destroy', $record))
        ->assertRedirect(route('admin.attendance.index'));

    $this->assertDatabaseMissing('attendance_records', ['id' => $record->id]);
});

it('validates all status values', function (string $status) {
    $this->actingAs($this->admin)->post(route('admin.attendance.store'), [
        'user_id' => $this->employee->id,
        'date' => '2026-03-08',
        'status' => $status,
    ])->assertRedirect();
})->with(['present', 'absent', 'late', 'half_day']);
