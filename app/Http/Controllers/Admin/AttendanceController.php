<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employeeId = request()->get('employee_id');
        $date = request()->get('date', now()->format('Y-m-d'));

        $query = AttendanceRecord::with('user');

        if ($employeeId) {
            $query->where('user_id', $employeeId);
        }

        if ($date) {
            $query->whereDate('date', $date);
        }

        $attendanceRecords = $query->latest('date')->paginate(15);
        $employees = User::where('role', 'employee')
            ->where('employment_status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.attendance.index', compact('attendanceRecords', 'employees', 'employeeId', 'date'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = User::where('role', 'employee')
            ->where('employment_status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.attendance.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'date' => ['required', 'date'],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i', 'after:check_in_time'],
            'status' => ['required', 'in:present,absent,late,half_day'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        AttendanceRecord::updateOrCreate(
            [
                'user_id' => $validated['user_id'],
                'date' => $validated['date'],
            ],
            [
                'check_in_time' => $validated['check_in_time'] ?? null,
                'check_out_time' => $validated['check_out_time'] ?? null,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance record created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttendanceRecord $attendance)
    {
        $attendanceRecord = $attendance->load('user');
        return view('admin.attendance.edit', compact('attendanceRecord'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AttendanceRecord $attendance)
    {
        $attendanceRecord = $attendance;

        $validated = $request->validate([
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i', 'after:check_in_time'],
            'status' => ['required', 'in:present,absent,late,half_day'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $attendanceRecord->update([
            'check_in_time' => $validated['check_in_time'] ?? null,
            'check_out_time' => $validated['check_out_time'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttendanceRecord $attendance)
    {
        $attendanceRecord = $attendance;
        $attendanceRecord->delete();

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Attendance record deleted successfully.');
    }
}
