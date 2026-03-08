<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Admin middleware
it('guest is redirected to login from admin routes', function () {
    $this->get('/admin/dashboard')->assertRedirect('/login');
});

it('employee is denied access to admin routes', function () {
    $employee = User::factory()->employee()->create();

    $this->actingAs($employee)->get('/admin/dashboard')->assertForbidden();
});

it('admin can access admin routes', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
});

// Employee middleware
it('guest is redirected to login from employee routes', function () {
    $this->get('/employee/dashboard')->assertRedirect('/login');
});

it('admin is denied access to employee routes', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/employee/dashboard')->assertForbidden();
});

it('employee can access employee routes', function () {
    $employee = User::factory()->employee()->create();

    $this->actingAs($employee)->get('/employee/dashboard')->assertOk();
});
