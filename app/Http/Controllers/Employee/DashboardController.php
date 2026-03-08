<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $stats = [
            'total_pay_slips' => $user->paySlips()->count(),
            'pending_leave_requests' => $user->leaveRequests()->where('status', 'pending')->count(),
            'approved_leave_requests' => $user->leaveRequests()->where('status', 'approved')->count(),
            'attendance_this_month' => $user->attendanceRecords()
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->where('status', 'present')
                ->count(),
        ];

        $recent_pay_slips = $user->paySlips()->latest()->limit(5)->get();
        $recent_leave_requests = $user->leaveRequests()->latest()->limit(5)->get();

        $leave_balances = LeaveBalance::where('user_id', $user->id)
            ->where('year', now()->year)
            ->get();

        $recent_attendance = $user->attendanceRecords()->latest('date')->limit(10)->get();

        return view('employee.dashboard', compact('stats', 'recent_pay_slips', 'recent_leave_requests', 'leave_balances', 'recent_attendance'));
    }
}
