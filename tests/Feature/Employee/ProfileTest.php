<?php

use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

it('passes leave balances to the profile view', function () {
    LeaveBalance::factory()->create([
        'user_id' => $this->employee->id,
        'leave_type' => 'vacation',
        'year' => now()->year,
        'remaining_days' => 7,
    ]);

    $response = $this->actingAs($this->employee)
        ->get(route('employee.profile.index'));

    $response->assertOk();
    expect($response->viewData('leaveBalances'))->toHaveCount(1);
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

it('updates date_of_birth', function () {
    $this->actingAs($this->employee)->put(route('employee.profile.update'), [
        'name' => $this->employee->name,
        'email' => $this->employee->email,
        'date_of_birth' => '1995-06-15',
    ])->assertRedirect(route('employee.profile.index'));

    $this->assertDatabaseHas('users', [
        'id' => $this->employee->id,
        'date_of_birth' => '1995-06-15',
    ]);
});

it('rejects a future date_of_birth', function () {
    $this->actingAs($this->employee)->put(route('employee.profile.update'), [
        'name' => $this->employee->name,
        'email' => $this->employee->email,
        'date_of_birth' => now()->addYear()->toDateString(),
    ])->assertSessionHasErrors('date_of_birth');
});

it('uploads a profile avatar', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

    $this->actingAs($this->employee)->put(route('employee.profile.update'), [
        'name' => $this->employee->name,
        'email' => $this->employee->email,
        'avatar' => $file,
    ])->assertRedirect(route('employee.profile.index'));

    $updatedUser = $this->employee->fresh();
    expect($updatedUser->avatar_path)->not->toBeNull();
    Storage::disk('public')->assertExists($updatedUser->avatar_path);
});

it('deletes the old avatar when uploading a new one', function () {
    Storage::fake('public');

    $oldFile = UploadedFile::fake()->image('old.jpg');
    $oldPath = $oldFile->store('avatars', 'public');
    $this->employee->update(['avatar_path' => $oldPath]);

    $newFile = UploadedFile::fake()->image('new.jpg', 200, 200);

    $this->actingAs($this->employee)->put(route('employee.profile.update'), [
        'name' => $this->employee->name,
        'email' => $this->employee->email,
        'avatar' => $newFile,
    ])->assertRedirect(route('employee.profile.index'));

    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($this->employee->fresh()->avatar_path);
});

it('rejects a non-image avatar upload', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->actingAs($this->employee)->put(route('employee.profile.update'), [
        'name' => $this->employee->name,
        'email' => $this->employee->email,
        'avatar' => $file,
    ])->assertSessionHasErrors('avatar');
});

it('rejects an avatar over 2MB', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('large.jpg')->size(3000);

    $this->actingAs($this->employee)->put(route('employee.profile.update'), [
        'name' => $this->employee->name,
        'email' => $this->employee->email,
        'avatar' => $file,
    ])->assertSessionHasErrors('avatar');
});

it('updates the password when provided', function () {
    $this->actingAs($this->employee)->put(route('employee.profile.update'), [
        'name' => $this->employee->name,
        'email' => $this->employee->email,
        'password' => 'NewPassword1!',
        'password_confirmation' => 'NewPassword1!',
    ])->assertRedirect(route('employee.profile.index'));

    expect(Hash::check('NewPassword1!', $this->employee->fresh()->password))->toBeTrue();
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
    User::factory()->employee()->create(['email' => 'taken@example.com']);

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
