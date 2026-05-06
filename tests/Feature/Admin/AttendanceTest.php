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

it('creates an attendance record with morning and afternoon session times', function () {
    $this->actingAs($this->admin)->post(route('admin.attendance.store'), [
        'user_id' => $this->employee->id,
        'date' => '2026-03-08',
        'morning_in' => '08:00',
        'morning_out' => '12:00',
        'afternoon_in' => '13:00',
        'afternoon_out' => '17:00',
        'status' => 'present',
    ])->assertRedirect(route('admin.attendance.index'));

    $this->assertDatabaseHas('attendance_records', [
        'user_id' => $this->employee->id,
        'date' => '2026-03-08',
        'morning_in' => '08:00',
        'morning_out' => '12:00',
        'afternoon_in' => '13:00',
        'afternoon_out' => '17:00',
        'status' => 'present',
    ]);
});

it('creates a half-day record with only morning session times', function () {
    $this->actingAs($this->admin)->post(route('admin.attendance.store'), [
        'user_id' => $this->employee->id,
        'date' => '2026-03-08',
        'morning_in' => '08:00',
        'morning_out' => '12:00',
        'status' => 'half_day',
    ])->assertRedirect(route('admin.attendance.index'));

    $record = AttendanceRecord::where('user_id', $this->employee->id)->where('date', '2026-03-08')->first();
    expect($record->status)->toBe('half_day')
        ->and($record->morning_in)->toStartWith('08:00')
        ->and($record->afternoon_in)->toBeNull();
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

it('rejects morning_out before morning_in', function () {
    $this->actingAs($this->admin)->post(route('admin.attendance.store'), [
        'user_id' => $this->employee->id,
        'date' => '2026-03-08',
        'morning_in' => '12:00',
        'morning_out' => '08:00',
        'status' => 'present',
    ])->assertSessionHasErrors('morning_out');
});

it('rejects afternoon_out before afternoon_in', function () {
    $this->actingAs($this->admin)->post(route('admin.attendance.store'), [
        'user_id' => $this->employee->id,
        'date' => '2026-03-08',
        'afternoon_in' => '17:00',
        'afternoon_out' => '13:00',
        'status' => 'present',
    ])->assertSessionHasErrors('afternoon_out');
});

it('shows the edit attendance form', function () {
    $record = AttendanceRecord::factory()->create(['user_id' => $this->employee->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.attendance.edit', $record))
        ->assertOk();
});

it('updates an attendance record with new session times', function () {
    $record = AttendanceRecord::factory()->present()->create(['user_id' => $this->employee->id]);

    $this->actingAs($this->admin)->put(route('admin.attendance.update', $record), [
        'status' => 'late',
        'morning_in' => '09:15',
        'morning_out' => '12:00',
        'afternoon_in' => '13:00',
        'afternoon_out' => '17:00',
    ])->assertRedirect(route('admin.attendance.index'));

    expect($record->fresh()->status)->toBe('late')
        ->and($record->fresh()->morning_in)->toStartWith('09:15');
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
