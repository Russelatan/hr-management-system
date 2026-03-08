<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAttendanceRequest;
use App\Http\Requests\Admin\UpdateAttendanceRequest;
use App\Models\AttendanceRecord;
use App\Models\User;

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
    public function store(StoreAttendanceRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();

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
    public function update(UpdateAttendanceRequest $request, AttendanceRecord $attendance): \Illuminate\Http\RedirectResponse
    {
        $attendanceRecord = $attendance;

        $validated = $request->validated();

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
