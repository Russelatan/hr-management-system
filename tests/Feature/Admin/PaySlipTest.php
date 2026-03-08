<?php

use App\Models\PaySlip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->employee = User::factory()->employee()->create();
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

it('creates a pay slip and calculates net salary', function () {
    $this->actingAs($this->admin)->post(route('admin.pay-slips.store'), [
        'user_id' => $this->employee->id,
        'month' => 3,
        'year' => 2026,
        'gross_salary' => 50000,
        'deductions' => 5000,
    ])->assertRedirect(route('admin.pay-slips.index'));

    $this->assertDatabaseHas('pay_slips', [
        'user_id' => $this->employee->id,
        'month' => 3,
        'year' => 2026,
        'gross_salary' => 50000,
        'deductions' => 5000,
        'net_salary' => 45000,
    ]);
});

it('rejects deductions exceeding gross salary', function () {
    $this->actingAs($this->admin)->post(route('admin.pay-slips.store'), [
        'user_id' => $this->employee->id,
        'month' => 3,
        'year' => 2026,
        'gross_salary' => 10000,
        'deductions' => 15000,
    ])->assertSessionHasErrors('deductions');
});

it('accepts a PDF file upload', function () {
    Storage::fake('local');

    $file = UploadedFile::fake()->create('payslip.pdf', 100, 'application/pdf');

    $this->actingAs($this->admin)->post(route('admin.pay-slips.store'), [
        'user_id' => $this->employee->id,
        'month' => 3,
        'year' => 2026,
        'gross_salary' => 50000,
        'deductions' => 0,
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
        'gross_salary' => 50000,
        'deductions' => 0,
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
        'gross_salary' => 50000,
    ];

    unset($data[$field]);

    $this->actingAs($this->admin)
        ->post(route('admin.pay-slips.store'), $data)
        ->assertSessionHasErrors($field);
})->with(['user_id', 'month', 'year', 'gross_salary']);
