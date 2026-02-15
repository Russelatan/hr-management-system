<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
            'leave_type' => ['required', 'in:sick,vacation,personal,other,maternity-leave,paternity-leave,bereavement-leave'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'hours_requested' => ['nullable', 'integer', 'min:1', 'max:8'],
            'reason' => ['nullable', 'string', 'max:500'],
            'document' => [
                'required_if:leave_type,maternity-leave',
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $daysRequested = $startDate->diffInDays($endDate) + 1;
        $hoursRequested = isset($validated['hours_requested']) ? (int) $validated['hours_requested'] : null;
        $isSickOrVacation = in_array($validated['leave_type'], LeaveRequest::leaveTypesWithHoursSupport());

        // For single-day partial leave: use hours only, days = 0
        if ($hoursRequested && $isSickOrVacation && $daysRequested === 1) {
            $daysRequested = 0;
        }

        // Check leave balance for sick/vacation
        if ($isSickOrVacation) {
            $balance = LeaveBalance::where('user_id', Auth::id())
                ->where('leave_type', $validated['leave_type'])
                ->where('year', now()->year)
                ->first();

            if ($hoursRequested) {
                if ($balance && $balance->remaining_hours < $hoursRequested) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Insufficient leave balance. You have {$balance->remaining_hours} hours remaining.");
                }
            } elseif ($balance && $balance->remaining_days < $daysRequested) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Insufficient leave balance. You have {$balance->remaining_days} days remaining.");
            }
        }

        $documentPath = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
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
        // Ensure employee can only view their own leave requests
        if ($leave_request->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $leaveRequest = $leave_request->load('approver');
        return view('employee.leave.show', compact('leaveRequest'));
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
