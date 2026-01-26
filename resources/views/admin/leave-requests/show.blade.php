@extends('layouts.app')

@section('title', 'Leave Request Details')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.leave-requests.index') }}" class="text-indigo-600 hover:text-indigo-900">← Back to Leave Requests</a>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h1 class="text-2xl font-semibold text-gray-900 mb-6">Leave Request Details</h1>

                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Employee</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $leaveRequest->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Leave Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($leaveRequest->leave_type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $leaveRequest->start_date->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">End Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $leaveRequest->end_date->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Days Requested</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $leaveRequest->days_requested }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($leaveRequest->status === 'approved') bg-green-100 text-green-800
                                @elseif($leaveRequest->status === 'rejected') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst($leaveRequest->status) }}
                            </span>
                        </dd>
                    </div>
                    @if($leaveRequest->reason)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Reason</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $leaveRequest->reason }}</dd>
                        </div>
                    @endif
                    @if($leaveRequest->approved_by && $leaveRequest->approver)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Approved By</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $leaveRequest->approver->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Approved At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $leaveRequest->approved_at?->format('M d, Y H:i') ?? 'N/A' }}</dd>
                        </div>
                    @endif
                </dl>

                @if($leaveRequest->status === 'pending')
                    <div class="mt-6 flex items-center justify-end gap-x-3">
                        <form action="{{ route('admin.leave-requests.reject', $leaveRequest) }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Reject
                            </button>
                        </form>
                        <form action="{{ route('admin.leave-requests.approve', $leaveRequest) }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                Approve
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
