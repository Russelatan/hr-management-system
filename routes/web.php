<?php

use App\Http\Controllers\Admin\LeaveBalanceController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        } else {
            return redirect('/employee/dashboard');
        }
    }

    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/avatars/{filename}', [\App\Http\Controllers\AvatarController::class, 'show'])
    ->where('filename', '[A-Za-z0-9._-]+')
    ->name('avatar.show');

// Password reset routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('employees', \App\Http\Controllers\Admin\EmployeeController::class);
    Route::resource('pay-slips', \App\Http\Controllers\Admin\PaySlipController::class);
    Route::get('pay-slips/{pay_slip}/download', [\App\Http\Controllers\Admin\PaySlipController::class, 'download'])->name('pay-slips.download');
    Route::get('leave-requests/{leave_request}/document', [\App\Http\Controllers\Admin\LeaveRequestController::class, 'downloadDocument'])->name('leave-requests.document');
    Route::resource('leave-requests', \App\Http\Controllers\Admin\LeaveRequestController::class)->only(['index', 'show']);
    Route::post('leave-requests/{leave_request}/approve', [\App\Http\Controllers\Admin\LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::post('leave-requests/{leave_request}/reject', [\App\Http\Controllers\Admin\LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    Route::resource('attendance', \App\Http\Controllers\Admin\AttendanceController::class);

    Route::get('leave-balances', [LeaveBalanceController::class, 'index'])->name('leave-balances.index');
    Route::get('leave-balances/{leave_balance}/edit', [LeaveBalanceController::class, 'edit'])->name('leave-balances.edit');
    Route::put('leave-balances/{leave_balance}', [LeaveBalanceController::class, 'update'])->name('leave-balances.update');
});

// Employee routes
Route::middleware(['auth', 'employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Employee\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/pay-slips', [\App\Http\Controllers\Employee\PaySlipController::class, 'index'])->name('pay-slips.index');
    Route::get('/pay-slips/{pay_slip}', [\App\Http\Controllers\Employee\PaySlipController::class, 'show'])->name('pay-slips.show');
    Route::get('/pay-slips/{pay_slip}/download', [\App\Http\Controllers\Employee\PaySlipController::class, 'download'])->name('pay-slips.download');

    Route::get('/leave', [\App\Http\Controllers\Employee\LeaveRequestController::class, 'index'])->name('leave.index');
    Route::get('/leave/create', [\App\Http\Controllers\Employee\LeaveRequestController::class, 'create'])->name('leave.create');
    Route::post('/leave', [\App\Http\Controllers\Employee\LeaveRequestController::class, 'store'])->name('leave.store');
    Route::get('/leave/{leave_request}/document', [\App\Http\Controllers\Employee\LeaveRequestController::class, 'downloadDocument'])->name('leave.document');
    Route::post('/leave/{leave_request}/cancel', [\App\Http\Controllers\Employee\LeaveRequestController::class, 'cancel'])->name('leave.cancel');
    Route::get('/leave/{leave_request}', [\App\Http\Controllers\Employee\LeaveRequestController::class, 'show'])->name('leave.show');

    Route::get('/attendance', [\App\Http\Controllers\Employee\AttendanceController::class, 'index'])->name('attendance.index');

    Route::get('/profile', [\App\Http\Controllers\Employee\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Employee\ProfileController::class, 'update'])->name('profile.update');
});
