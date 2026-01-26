<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leaveRequests = LeaveRequest::where('user_id', Auth::id())
            ->with('approver')
            ->latest()
            ->paginate(15);

        $leaveBalances = LeaveBalance::where('user_id', Auth::id())
            ->where('year', now()->year)
            ->get();

        return view('employee.leave.index', compact('leaveRequests', 'leaveBalances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $leaveBalances = LeaveBalance::where('user_id', Auth::id())
            ->where('year', now()->year)
            ->get();

        return view('employee.leave.create', compact('leaveBalances'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type' => ['required', 'in:sick,vacation,personal,other'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $daysRequested = $startDate->diffInDays($endDate) + 1;

        // Check leave balance
        $balance = LeaveBalance::where('user_id', Auth::id())
            ->where('leave_type', $validated['leave_type'])
            ->where('year', now()->year)
            ->first();

        if ($balance && $balance->remaining_days < $daysRequested) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Insufficient leave balance. You have {$balance->remaining_days} days remaining.");
        }

        LeaveRequest::create([
            'user_id' => Auth::id(),
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days_requested' => $daysRequested,
            'reason' => $validated['reason'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('employee.leave.index')
            ->with('success', 'Leave request submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveRequest $leave_request)
    {
        // Ensure employee can only view their own leave requests
        if ($leave_request->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $leaveRequest = $leave_request->load('approver');
        return view('employee.leave.show', compact('leaveRequest'));
    }
}
