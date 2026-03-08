<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->employee = User::factory()->employee()->create();
});

it('shows the profile page', function () {
    $this->actingAs($this->employee)
        ->get(route('employee.profile.index'))
        ->assertOk()
        ->assertViewIs('employee.profile.index');
});

it('updates the profile name and phone', function () {
    $this->actingAs($this->employee)->put(route('employee.profile.update'), [
        'name' => 'Updated Name',
        'email' => $this->employee->email,
        'phone' => '09171234567',
    ])->assertRedirect(route('employee.profile.index'));

    $this->assertDatabaseHas('users', [
        'id' => $this->employee->id,
        'name' => 'Updated Name',
        'phone' => '09171234567',
    ]);
});

it('updates the password when provided', function () {
    $this->actingAs($this->employee)->put(route('employee.profile.update'), [
        'name' => $this->employee->name,
        'email' => $this->employee->email,
        'password' => 'NewPassword1!',
        'password_confirmation' => 'NewPassword1!',
    ])->assertRedirect(route('employee.profile.index'));

    $this->assertTrue(
        \Illuminate\Support\Facades\Hash::check('NewPassword1!', $this->employee->fresh()->password)
    );
});

it('rejects a mismatched password confirmation', function () {
    $this->actingAs($this->employee)->put(route('employee.profile.update'), [
        'name' => $this->employee->name,
        'email' => $this->employee->email,
        'password' => 'NewPassword1!',
        'password_confirmation' => 'DifferentPassword1!',
    ])->assertSessionHasErrors('password');
});

it('rejects duplicate email from another account', function () {
    $other = User::factory()->employee()->create(['email' => 'taken@example.com']);

    $this->actingAs($this->employee)->put(route('employee.profile.update'), [
        'name' => $this->employee->name,
        'email' => 'taken@example.com',
    ])->assertSessionHasErrors('email');
});

it('cannot update role or employment status through profile update', function () {
    $originalRole = $this->employee->role;

    $this->actingAs($this->employee)->put(route('employee.profile.update'), [
        'name' => $this->employee->name,
        'email' => $this->employee->email,
        'role' => 'admin',
        'employment_status' => 'terminated',
    ])->assertRedirect(route('employee.profile.index'));

    expect($this->employee->fresh()->role)->toBe($originalRole);
    expect($this->employee->fresh()->employment_status)->not->toBe('terminated');
});
