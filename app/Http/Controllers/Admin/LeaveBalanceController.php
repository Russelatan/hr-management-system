<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateLeaveBalanceRequest;
use App\Models\LeaveBalance;
use App\Models\User;

class LeaveBalanceController extends Controller
{
    /**
     * Display a listing of leave balances.
     */
    public function index()
    {
        $employeeId = request()->get('employee_id');
        $year = request()->get('year', now()->year);

        $query = LeaveBalance::with('user')
            ->where('year', $year)
            ->orderBy('leave_type');

        if ($employeeId) {
            $query->where('user_id', $employeeId);
        }

        $leaveBalances = $query->paginate(20);
        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('admin.leave-balances.index', compact('leaveBalances', 'employees', 'employeeId', 'year'));
    }

    /**
     * Show the form for editing a leave balance.
     */
    public function edit(LeaveBalance $leave_balance)
    {
        $leaveBalance = $leave_balance->load('user');

        return view('admin.leave-balances.edit', compact('leaveBalance'));
    }

    /**
     * Update the leave balance.
     */
    public function update(UpdateLeaveBalanceRequest $request, LeaveBalance $leave_balance): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();

        $remainingDays = max(0, $validated['total_days'] - $validated['used_days']);
        $remainingHours = max(0, $validated['total_hours'] - $validated['used_hours']);

        $leave_balance->update([
            'total_days' => $validated['total_days'],
            'used_days' => $validated['used_days'],
            'remaining_days' => $remainingDays,
            'total_hours' => $validated['total_hours'],
            'used_hours' => $validated['used_hours'],
            'remaining_hours' => $remainingHours,
        ]);

        return redirect()->route('admin.leave-balances.index')
            ->with('success', 'Leave balance updated successfully.');
    }
}
