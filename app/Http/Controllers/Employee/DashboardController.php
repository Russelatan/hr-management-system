<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\PaySlip;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\AttendanceRecord;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_pay_slips' => PaySlip::where('user_id', $user->id)->count(),
            'pending_leave_requests' => LeaveRequest::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved_leave_requests' => LeaveRequest::where('user_id', $user->id)->where('status', 'approved')->count(),
            'attendance_this_month' => AttendanceRecord::where('user_id', $user->id)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->where('status', 'present')
                ->count(),
        ];

        $recent_pay_slips = PaySlip::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        $recent_leave_requests = LeaveRequest::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        $leave_balances = LeaveBalance::where('user_id', $user->id)
            ->where('year', now()->year)
            ->get();

        $recent_attendance = AttendanceRecord::where('user_id', $user->id)
            ->latest('date')
            ->limit(10)
            ->get();

        return view('employee.dashboard', compact('stats', 'recent_pay_slips', 'recent_leave_requests', 'leave_balances', 'recent_attendance'));
    }
}
