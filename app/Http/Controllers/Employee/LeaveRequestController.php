<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreLeaveRequest;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
    public function store(StoreLeaveRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        // Count only weekdays (Mon–Fri), excluding weekends
        $daysRequested = $startDate->diffInWeekdays($endDate) + ($startDate->isWeekday() ? 1 : 0);

        $hoursRequested = isset($validated['hours_requested']) ? (int) $validated['hours_requested'] : null;
        $isSickOrVacation = in_array($validated['leave_type'], LeaveRequest::leaveTypesWithHoursSupport());

        // For single-day partial leave: use hours only, days = 0
        if ($hoursRequested && $isSickOrVacation && $daysRequested === 1) {
            $daysRequested = 0;
        }

        // Overlap detection — reject if any approved or pending leave overlaps
        $hasOverlap = LeaveRequest::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->where('start_date', '<=', $validated['end_date'])
            ->where('end_date', '>=', $validated['start_date'])
            ->exists();

        if ($hasOverlap) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'You already have a pending or approved leave request that overlaps with these dates.');
        }

        // Check leave balance for all types that have a balance record
        $leaveYear = $startDate->year;
        $balance = LeaveBalance::where('user_id', Auth::id())
            ->where('leave_type', $validated['leave_type'])
            ->where('year', $leaveYear)
            ->first();

        if ($balance) {
            if ($hoursRequested) {
                if ($balance->remaining_hours < $hoursRequested) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Insufficient leave balance. You have {$balance->remaining_hours} hours remaining.");
                }
            } elseif ($balance->remaining_days < $daysRequested) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Insufficient leave balance. You have {$balance->remaining_days} days remaining.");
            }
        }

        $documentPath = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $documentPath = $file->storeAs('leave-documents', $filename, 'local');
        }

        LeaveRequest::create([
            'user_id' => Auth::id(),
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days_requested' => $daysRequested,
            'hours_requested' => $hoursRequested,
            'reason' => $validated['reason'] ?? null,
            'document_path' => $documentPath,
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
        if ($leave_request->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $leaveRequest = $leave_request->load('approver');

        return view('employee.leave.show', compact('leaveRequest'));
    }

    /**
     * Cancel a pending leave request.
     */
    public function cancel(LeaveRequest $leave_request): RedirectResponse
    {
        if ($leave_request->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        if ($leave_request->status !== 'pending') {
            abort(422, 'Only pending leave requests can be cancelled.');
        }

        $leave_request->update(['status' => 'cancelled']);

        return redirect()->route('employee.leave.index')
            ->with('success', 'Leave request cancelled successfully.');
    }

    /**
     * Download the supporting document for own leave request.
     */
    public function downloadDocument(LeaveRequest $leave_request)
    {
        if ($leave_request->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        if (! $leave_request->hasDocument()) {
            abort(404, 'No document attached to this leave request.');
        }

        $path = Storage::disk('local')->path($leave_request->document_path);
        if (! file_exists($path)) {
            abort(404, 'Document file not found.');
        }

        $fileName = basename($leave_request->document_path);

        return response()->download($path, $fileName, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }
}
