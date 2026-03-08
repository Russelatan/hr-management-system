<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $status = request()->get('status', 'all');

        $query = LeaveRequest::with(['user', 'approver']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $leaveRequests = $query->latest()->paginate(15);

        return view('admin.leave-requests.index', compact('leaveRequests', 'status'));
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveRequest $leave_request)
    {
        $leaveRequest = $leave_request->load(['user', 'approver']);

        return view('admin.leave-requests.show', compact('leaveRequest'));
    }

    /**
     * Approve a leave request.
     */
    public function approve(Request $request, LeaveRequest $leave_request)
    {
        $leaveRequest = $leave_request;

        if ($leaveRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'This leave request has already been processed.');
        }

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Update leave balance
        $balance = LeaveBalance::firstOrCreate(
            [
                'user_id' => $leaveRequest->user_id,
                'leave_type' => $leaveRequest->leave_type,
                'year' => now()->year,
            ],
            [
                'total_days' => 0,
                'used_days' => 0,
                'remaining_days' => 0,
                'total_hours' => 0,
                'used_hours' => 0,
                'remaining_hours' => 0,
            ]
        );

        if ($leaveRequest->hours_requested) {
            $balance->increment('used_hours', $leaveRequest->hours_requested);
            $balance->decrement('remaining_hours', $leaveRequest->hours_requested);
        } else {
            $balance->increment('used_days', $leaveRequest->days_requested);
            $balance->decrement('remaining_days', $leaveRequest->days_requested);
        }

        return redirect()->route('admin.leave-requests.index')
            ->with('success', 'Leave request approved successfully.');
    }

    /**
     * Reject a leave request.
     */
    public function reject(Request $request, LeaveRequest $leave_request)
    {
        $leaveRequest = $leave_request;

        if ($leaveRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'This leave request has already been processed.');
        }

        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.leave-requests.index')
            ->with('success', 'Leave request rejected.');
    }

    /**
     * Download the supporting document for a leave request.
     */
    public function downloadDocument(LeaveRequest $leave_request)
    {
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
