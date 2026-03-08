<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Login
it('shows the login form', function () {
    $this->get('/login')->assertOk();
});

it('logs in an admin and redirects to admin dashboard', function () {
    $admin = User::factory()->admin()->create(['password' => bcrypt('password')]);

    $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
        ->assertRedirect('/admin/dashboard');
});

it('logs in an employee and redirects to employee dashboard', function () {
    $employee = User::factory()->employee()->create(['password' => bcrypt('password')]);

    $this->post('/login', ['email' => $employee->email, 'password' => 'password'])
        ->assertRedirect('/employee/dashboard');
});

it('rejects invalid credentials', function () {
    User::factory()->create(['email' => 'user@example.com', 'password' => bcrypt('correct')]);

    $this->post('/login', ['email' => 'user@example.com', 'password' => 'wrong'])
        ->assertSessionHasErrors();
});

it('logs out the user', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)->post('/logout')->assertRedirect('/login');

    $this->assertGuest();
});

// Forgot password
it('shows the forgot password form', function () {
    $this->get('/forgot-password')->assertOk();
});

it('validates email on forgot password form', function () {
    $this->post('/forgot-password', ['email' => 'not-an-email'])
        ->assertSessionHasErrors('email');
});

it('sends reset link for known email', function () {
    $user = User::factory()->create();

    \Illuminate\Support\Facades\Password::shouldReceive('sendResetLink')
        ->once()
        ->andReturn(\Illuminate\Support\Facades\Password::RESET_LINK_SENT);

    $this->post('/forgot-password', ['email' => $user->email])
        ->assertSessionHas('status');
});

// Reset password
it('shows the reset password form with a token', function () {
    $this->get('/reset-password/some-token')->assertOk();
});

// Registration
it('admin can access the register form', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/admin/register')->assertOk();
});

it('guest is redirected to login from the register form', function () {
    $this->get('/admin/register')->assertRedirect('/login');
});

it('employee cannot access the register form', function () {
    $employee = User::factory()->employee()->create();

    $this->actingAs($employee)->get('/admin/register')->assertForbidden();
});

it('admin can register a new user', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post('/admin/register', [
        'name' => 'New Employee',
        'email' => 'newemployee@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
        'role' => 'employee',
        'employment_status' => 'active',
    ])->assertRedirect(route('admin.employees.index'));

    $this->assertDatabaseHas('users', ['email' => 'newemployee@example.com']);
});

it('registration requires a valid role', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post('/admin/register', [
        'name' => 'User',
        'email' => 'user@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
        'role' => 'superuser',
    ])->assertSessionHasErrors('role');
});
