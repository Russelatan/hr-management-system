<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\View\View
    {
        $startDate = request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = request()->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $attendanceRecords = AttendanceRecord::where('user_id', Auth::id())
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->paginate(15);

        $statRow = AttendanceRecord::query()
            ->where('user_id', Auth::id())
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total_days,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as late_days
            ', ['present', 'absent', 'late'])
            ->first();

        $stats = [
            'total_days' => (int) ($statRow->total_days ?? 0),
            'present_days' => (int) ($statRow->present_days ?? 0),
            'absent_days' => (int) ($statRow->absent_days ?? 0),
            'late_days' => (int) ($statRow->late_days ?? 0),
        ];

        return view('employee.attendance.index', compact('attendanceRecords', 'stats', 'startDate', 'endDate'));
    }
}
