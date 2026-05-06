<?php

use App\Models\AttendanceRecord;
use App\Models\PaySlip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->employee = User::factory()->employee()->create([
        'basic_salary' => 50000.00,
        'sss_contribution' => 1125.00,
        'philhealth_contribution' => 625.00,
        'pagibig_contribution' => 100.00,
        'other_deductions' => 0.00,
        'working_days_per_month' => 22,
    ]);
});

it('lists pay slips', function () {
    PaySlip::factory()->count(3)->create(['user_id' => $this->employee->id, 'created_by' => $this->admin->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.pay-slips.index'))
        ->assertOk()
        ->assertViewIs('admin.pay-slips.index');
});

it('shows the create pay slip form', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.pay-slips.create'))
        ->assertOk();
});

it('auto-computes and creates a pay slip from employee salary data', function () {
    $this->actingAs($this->admin)->post(route('admin.pay-slips.store'), [
        'user_id' => $this->employee->id,
        'month' => 3,
        'year' => 2026,
    ])->assertRedirect(route('admin.pay-slips.index'));

    $this->assertDatabaseHas('pay_slips', [
        'user_id' => $this->employee->id,
        'month' => 3,
        'year' => 2026,
        'gross_salary' => 50000.00,
        'deductions' => 1850.00,
        'net_salary' => 48150.00,
    ]);
});

it('deducts absent days from gross salary during computation', function () {
    AttendanceRecord::factory()->create([
        'user_id' => $this->employee->id,
        'date' => '2026-03-05',
        'status' => 'absent',
    ]);

    $this->actingAs($this->admin)->post(route('admin.pay-slips.store'), [
        'user_id' => $this->employee->id,
        'month' => 3,
        'year' => 2026,
    ])->assertRedirect(route('admin.pay-slips.index'));

    $paySlip = PaySlip::where('user_id', $this->employee->id)->first();
    $dailyRate = round(50000 / 22, 2);
    $expectedGross = round(50000 - $dailyRate, 2);

    expect((float) $paySlip->gross_salary)->toBe($expectedGross)
        ->and($paySlip->computation_notes['absent_days'])->toBe(1);
});

it('stores computation notes on the pay slip', function () {
    $this->actingAs($this->admin)->post(route('admin.pay-slips.store'), [
        'user_id' => $this->employee->id,
        'month' => 3,
        'year' => 2026,
    ])->assertRedirect();

    $paySlip = PaySlip::where('user_id', $this->employee->id)->first();

    expect($paySlip->computation_notes)->toBeArray()
        ->toHaveKey('basic_salary')
        ->toHaveKey('absent_days')
        ->toHaveKey('gross_salary')
        ->toHaveKey('net_salary');
});

it('rejects generating a pay slip for an employee without a basic salary', function () {
    $employeeWithoutSalary = User::factory()->employeeWithoutSalary()->create();

    $this->actingAs($this->admin)->post(route('admin.pay-slips.store'), [
        'user_id' => $employeeWithoutSalary->id,
        'month' => 3,
        'year' => 2026,
    ])->assertSessionHasErrors('user_id');
});

it('accepts a PDF file upload alongside the auto-computed pay slip', function () {
    Storage::fake('local');

    $file = UploadedFile::fake()->create('payslip.pdf', 100, 'application/pdf');

    $this->actingAs($this->admin)->post(route('admin.pay-slips.store'), [
        'user_id' => $this->employee->id,
        'month' => 3,
        'year' => 2026,
        'file' => $file,
    ])->assertRedirect();

    $paySlip = PaySlip::first();
    expect($paySlip->file_path)->not->toBeNull();
    Storage::disk('local')->assertExists($paySlip->file_path);
});

it('rejects non-PDF file uploads', function () {
    $file = UploadedFile::fake()->create('document.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

    $this->actingAs($this->admin)->post(route('admin.pay-slips.store'), [
        'user_id' => $this->employee->id,
        'month' => 3,
        'year' => 2026,
        'file' => $file,
    ])->assertSessionHasErrors('file');
});

it('shows a pay slip detail', function () {
    $paySlip = PaySlip::factory()->create(['user_id' => $this->employee->id, 'created_by' => $this->admin->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.pay-slips.show', $paySlip))
        ->assertOk();
});

it('deletes a pay slip', function () {
    $paySlip = PaySlip::factory()->create(['user_id' => $this->employee->id, 'created_by' => $this->admin->id]);

    $this->actingAs($this->admin)
        ->delete(route('admin.pay-slips.destroy', $paySlip))
        ->assertRedirect(route('admin.pay-slips.index'));

    $this->assertDatabaseMissing('pay_slips', ['id' => $paySlip->id]);
});

it('validates required fields on create', function (string $field) {
    $data = [
        'user_id' => $this->employee->id,
        'month' => 3,
        'year' => 2026,
    ];

    unset($data[$field]);

    $this->actingAs($this->admin)
        ->post(route('admin.pay-slips.store'), $data)
        ->assertSessionHasErrors($field);
})->with(['user_id', 'month', 'year']);
