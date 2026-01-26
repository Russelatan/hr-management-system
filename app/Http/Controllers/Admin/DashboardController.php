<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PaySlip;
use App\Models\LeaveRequest;
use App\Models\AttendanceRecord;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_employees' => User::where('role', 'employee')->count(),
            'active_employees' => User::where('role', 'employee')->where('employment_status', 'active')->count(),
            'pending_leave_requests' => LeaveRequest::where('status', 'pending')->count(),
            'recent_pay_slips' => PaySlip::whereMonth('created_at', now()->month)->count(),
        ];

        $recent_leave_requests = LeaveRequest::with('user')
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        $recent_employees = User::where('role', 'employee')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_leave_requests', 'recent_employees'));
    }
}
