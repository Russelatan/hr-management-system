<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $startDate = request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = request()->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $attendanceRecords = AttendanceRecord::where('user_id', Auth::id())
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->paginate(15);

        $stats = [
            'total_days' => AttendanceRecord::where('user_id', Auth::id())
                ->whereBetween('date', [$startDate, $endDate])
                ->count(),
            'present_days' => AttendanceRecord::where('user_id', Auth::id())
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'present')
                ->count(),
            'absent_days' => AttendanceRecord::where('user_id', Auth::id())
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'absent')
                ->count(),
            'late_days' => AttendanceRecord::where('user_id', Auth::id())
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'late')
                ->count(),
        ];

        return view('employee.attendance.index', compact('attendanceRecords', 'stats', 'startDate', 'endDate'));
    }
}
