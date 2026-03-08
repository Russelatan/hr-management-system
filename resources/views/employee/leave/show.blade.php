@extends('layouts.app')

@section('title', 'Leave Request Details')

@section('content')
    <x-page-header title="Leave Request Details">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('employee.leave.index')">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card>
        <dl class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Leave Type</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('-', ' ', $leaveRequest->leave_type)) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Start Date</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveRequest->start_date->format('M d, Y') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">End Date</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveRequest->end_date->format('M d, Y') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    @if($leaveRequest->hours_requested)
                        {{ $leaveRequest->hours_requested }} hours
                    @else
                        {{ $leaveRequest->days_requested }} day(s)
                    @endif
                </dd>
            </div>
            @if($leaveRequest->hasDocument())
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Supporting Document</dt>
                    <dd class="mt-1">
                        <a href="{{ route('employee.leave.document', $leaveRequest) }}" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Download Document
                        </a>
                    </dd>
                </div>
            @endif
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                <dd class="mt-1"><x-status-badge :status="$leaveRequest->status" /></dd>
            </div>
            @if($leaveRequest->reason)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Reason</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveRequest->reason }}</dd>
                </div>
            @endif
            @if($leaveRequest->approved_by && $leaveRequest->approver)
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved By</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveRequest->approver->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved At</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveRequest->approved_at?->format('M d, Y H:i') ?? 'N/A' }}</dd>
                </div>
            @endif
        </dl>
    </x-card>
@endsection
