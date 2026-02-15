@extends('layouts.app')

@section('title', 'Leave Request Details')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('employee.leave.index') }}" class="text-indigo-600 hover:text-indigo-900">← Back to Leave Requests</a>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h1 class="text-2xl font-semibold text-gray-900 mb-6">Leave Request Details</h1>

                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Leave Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('-', ' ', $leaveRequest->leave_type)) }}</dd>
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
                        <dt class="text-sm font-medium text-gray-500">Duration</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($leaveRequest->hours_requested)
                                {{ $leaveRequest->hours_requested }} hours
                            @else
                                {{ $leaveRequest->days_requested }} day(s)
                            @endif
                        </dd>
                    </div>
                    @if($leaveRequest->hasDocument())
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Supporting Document</dt>
                            <dd class="mt-1">
                                <a href="{{ route('employee.leave.document', $leaveRequest) }}" class="text-indigo-600 hover:text-indigo-900">
                                    Download Document
                                </a>
                            </dd>
                        </div>
                    @endif
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
            </div>
        </div>
    </div>
</div>
@endsection
