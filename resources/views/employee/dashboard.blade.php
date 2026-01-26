@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-3xl font-semibold text-gray-900">Welcome, {{ Auth::user()->name }}</h1>
            <p class="mt-2 text-sm text-gray-700">Your HR dashboard overview</p>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Pay Slips</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['total_pay_slips'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Leave</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['pending_leave_requests'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Approved Leave</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['approved_leave_requests'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Attendance This Month</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['attendance_this_month'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Pay Slips</h3>
                    @if($recent_pay_slips->count() > 0)
                        <div class="flow-root">
                            <ul class="-my-5 divide-y divide-gray-200">
                                @foreach($recent_pay_slips as $paySlip)
                                    <li class="py-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900">{{ $paySlip->month }}/{{ $paySlip->year }}</p>
                                                <p class="text-sm text-gray-500">Net Salary: ${{ number_format($paySlip->net_salary, 2) }}</p>
                                            </div>
                                            <div>
                                                <a href="{{ route('employee.pay-slips.show', $paySlip) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">View</a>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('employee.pay-slips.index') }}" class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                View all pay slips
                            </a>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No pay slips yet</p>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Leave Balances</h3>
                    @if($leave_balances->count() > 0)
                        <div class="space-y-4">
                            @foreach($leave_balances as $balance)
                                <div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-900 font-medium">{{ ucfirst($balance->leave_type) }}</span>
                                        <span class="text-gray-500">{{ $balance->remaining_days }} / {{ $balance->total_days }} days</span>
                                    </div>
                                    <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                                        @php
                                            $percentage = $balance->total_days > 0 ? round(($balance->remaining_days / $balance->total_days) * 100, 2) : 0;
                                        @endphp
                                        <div class="bg-indigo-600 h-2 rounded-full progress-bar" data-percentage="{{ $percentage }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No leave balances set</p>
                    @endif
                    <div class="mt-6">
                        <a href="{{ route('employee.leave.index') }}" class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Manage Leave
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Leave Requests</h3>
                @if($recent_leave_requests->count() > 0)
                    <div class="flow-root">
                        <ul class="-my-5 divide-y divide-gray-200">
                            @foreach($recent_leave_requests as $request)
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">{{ ucfirst($request->leave_type) }}</p>
                                            <p class="text-sm text-gray-500">{{ $request->start_date->format('M d') }} - {{ $request->end_date->format('M d, Y') }}</p>
                                        </div>
                                        <div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($request->status === 'approved') bg-green-100 text-green-800
                                                @elseif($request->status === 'rejected') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No leave requests yet</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .progress-bar[data-percentage] {
        width: var(--percentage);
    }
</style>
@endpush

@push('scripts')
<script>
    document.querySelectorAll('.progress-bar[data-percentage]').forEach(function(el) {
        el.style.width = el.getAttribute('data-percentage') + '%';
    });
</script>
@endpush
@endsection
