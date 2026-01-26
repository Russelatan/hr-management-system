<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
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

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('employees', \App\Http\Controllers\Admin\EmployeeController::class);
    Route::resource('pay-slips', \App\Http\Controllers\Admin\PaySlipController::class);
    Route::get('pay-slips/{pay_slip}/download', [\App\Http\Controllers\Admin\PaySlipController::class, 'download'])->name('pay-slips.download');
    Route::resource('leave-requests', \App\Http\Controllers\Admin\LeaveRequestController::class)->only(['index', 'show']);
    Route::post('leave-requests/{leave_request}/approve', [\App\Http\Controllers\Admin\LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::post('leave-requests/{leave_request}/reject', [\App\Http\Controllers\Admin\LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    Route::resource('attendance', \App\Http\Controllers\Admin\AttendanceController::class);
});

// Employee routes
Route::middleware(['auth', 'employee'])->prefix('employee')->name('employee.')->group(function () {
    // Employee routes will be added in Phase 5
});
