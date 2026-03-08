<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

it('lists employees', function () {
    User::factory()->employee()->count(3)->create();

    $this->actingAs($this->admin)
        ->get(route('admin.employees.index'))
        ->assertOk()
        ->assertViewIs('admin.employees.index');
});

it('shows the create employee form', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.employees.create'))
        ->assertOk();
});

it('creates an employee with valid data', function () {
    $this->actingAs($this->admin)->post(route('admin.employees.store'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
        'employment_status' => 'active',
        'employment_type' => 'full-time',
    ])->assertRedirect(route('admin.employees.index'));

    $this->assertDatabaseHas('users', ['email' => 'jane@example.com', 'role' => 'employee']);
});

it('auto-generates employee_id when not provided', function () {
    $this->actingAs($this->admin)->post(route('admin.employees.store'), [
        'name' => 'Auto ID',
        'email' => 'autoid@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
        'employment_status' => 'active',
    ]);

    $employee = User::where('email', 'autoid@example.com')->first();
    expect($employee->employee_id)->toStartWith('EMP');
});

it('rejects duplicate email on create', function () {
    User::factory()->employee()->create(['email' => 'taken@example.com']);

    $this->actingAs($this->admin)->post(route('admin.employees.store'), [
        'name' => 'Another',
        'email' => 'taken@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
        'employment_status' => 'active',
    ])->assertSessionHasErrors('email');
});

it('shows employee detail', function () {
    $employee = User::factory()->employee()->create();

    $this->actingAs($this->admin)
        ->get(route('admin.employees.show', $employee))
        ->assertOk()
        ->assertViewIs('admin.employees.show');
});

it('shows the edit employee form', function () {
    $employee = User::factory()->employee()->create();

    $this->actingAs($this->admin)
        ->get(route('admin.employees.edit', $employee))
        ->assertOk();
});

it('updates an employee', function () {
    $employee = User::factory()->employee()->create();

    $this->actingAs($this->admin)->put(route('admin.employees.update', $employee), [
        'name' => 'Updated Name',
        'email' => $employee->email,
        'employment_status' => 'on_leave',
    ])->assertRedirect(route('admin.employees.index'));

    $this->assertDatabaseHas('users', ['id' => $employee->id, 'name' => 'Updated Name', 'employment_status' => 'on_leave']);
});

it('deletes an employee', function () {
    $employee = User::factory()->employee()->create();

    $this->actingAs($this->admin)
        ->delete(route('admin.employees.destroy', $employee))
        ->assertRedirect(route('admin.employees.index'));

    $this->assertDatabaseMissing('users', ['id' => $employee->id]);
});

it('cannot delete an admin user via employee route', function () {
    $anotherAdmin = User::factory()->admin()->create();

    $this->actingAs($this->admin)
        ->delete(route('admin.employees.destroy', $anotherAdmin))
        ->assertNotFound();
});
