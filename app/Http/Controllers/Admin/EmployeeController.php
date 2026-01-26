<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = User::where('role', 'employee')
            ->latest()
            ->paginate(15);

        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'employee_id' => ['nullable', 'string', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'hire_date' => ['nullable', 'date'],
            'employment_status' => ['nullable', 'in:active,on_leave,terminated,suspended'],
        ]);

        // Auto-generate employee_id if not provided
        $employeeId = $validated['employee_id'] ?? $this->generateEmployeeId($validated['hire_date'] ?? now());

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'employee',
            'employee_id' => $employeeId,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'hire_date' => $validated['hire_date'] ?? null,
            'employment_status' => $validated['employment_status'] ?? 'active',
        ]);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Generate a unique employee ID based on hire date.
     * Format: EMP{YYYYMMDD}-{NNN}
     * 
     * @param \DateTime|string $hireDate
     * @return string
     */
    private function generateEmployeeId($hireDate): string
    {
        // Convert to Carbon instance if string
        if (is_string($hireDate)) {
            $hireDate = Carbon::parse($hireDate);
        }

        // Format date as YYYYMMDD
        $datePrefix = $hireDate->format('Ymd');
        $prefix = "EMP{$datePrefix}-";

        // Find existing IDs with the same prefix
        $existingIds = User::where('employee_id', 'like', $prefix . '%')
            ->whereNotNull('employee_id')
            ->pluck('employee_id')
            ->toArray();

        // Extract sequence numbers
        $sequences = [];
        foreach ($existingIds as $id) {
            if (preg_match('/' . preg_quote($prefix, '/') . '(\d+)$/', $id, $matches)) {
                $sequences[] = (int) $matches[1];
            }
        }

        // Find next available sequence number
        $nextSequence = 1;
        if (!empty($sequences)) {
            $maxSequence = max($sequences);
            $nextSequence = $maxSequence + 1;
        }

        // Generate ID with 3-digit sequence
        $employeeId = $prefix . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);

        // Ensure uniqueness (handle race conditions)
        $attempts = 0;
        while (User::where('employee_id', $employeeId)->exists() && $attempts < 10) {
            $nextSequence++;
            $employeeId = $prefix . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
            $attempts++;
        }

        return $employeeId;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $employee)
    {
        $employee = User::where('role', 'employee')->findOrFail($employee);
        
        $paySlips = $employee->paySlips()->latest()->limit(5)->get();
        $leaveRequests = $employee->leaveRequests()->latest()->limit(5)->get();
        $attendanceRecords = $employee->attendanceRecords()->latest()->limit(10)->get();

        return view('admin.employees.show', compact('employee', 'paySlips', 'leaveRequests', 'attendanceRecords'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $employee)
    {
        $employee = User::where('role', 'employee')->findOrFail($employee);
        return view('admin.employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $employee)
    {
        $employee = User::where('role', 'employee')->findOrFail($employee);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $employee->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            // employee_id is not validated in update - it's read-only
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'hire_date' => ['nullable', 'date'],
            'employment_status' => ['required', 'in:active,on_leave,terminated,suspended'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            // Don't update employee_id - it's auto-generated and should remain unchanged
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'hire_date' => $validated['hire_date'] ?? null,
            'employment_status' => $validated['employment_status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $employee->update($updateData);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $employee)
    {
        $employee = User::where('role', 'employee')->findOrFail($employee);
        $employee->delete();

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
