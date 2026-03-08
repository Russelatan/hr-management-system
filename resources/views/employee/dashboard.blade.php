@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
    <x-page-header title="Welcome, {{ Auth::user()->name }}" description="Your HR dashboard overview" />

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Total Pay Slips" :value="$stats['total_pay_slips']" color="blue" icon='<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />' />
        <x-stat-card label="Pending Leave" :value="$stats['pending_leave_requests']" color="yellow" icon='<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />' />
        <x-stat-card label="Approved Leave" :value="$stats['approved_leave_requests']" color="green" icon='<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />' />
        <x-stat-card label="Attendance This Month" :value="$stats['attendance_this_month']" color="indigo" icon='<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />' />
    </div>

    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-card title="Recent Pay Slips">
                @if($recent_pay_slips->count() > 0)
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($recent_pay_slips as $paySlip)
                            <li class="flex items-center justify-between py-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $paySlip->month }}/{{ $paySlip->year }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Net Salary: ₱{{ number_format($paySlip->net_salary, 2) }}</p>
                                </div>
                                <a href="{{ route('employee.pay-slips.show', $paySlip) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">View</a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-700">
                        <x-button variant="secondary" :href="route('employee.pay-slips.index')" class="w-full justify-center">View all pay slips</x-button>
                    </div>
                @else
                    <x-empty-state message="No pay slips yet" icon='<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />' />
                @endif
            </x-card>
        </div>

        <div>
            <x-card title="Leave Balances">
                @if($leave_balances->count() > 0)
                    <div class="space-y-4">
                        @foreach($leave_balances as $balance)
                            @php
                                $percentage = $balance->total_days > 0 ? round(($balance->remaining_days / $balance->total_days) * 100, 2) : 0;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ ucfirst($balance->leave_type) }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">{{ $balance->remaining_days }} / {{ $balance->total_days }}</span>
                                </div>
                                <div class="mt-1.5 h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                    <div class="h-2 rounded-full bg-indigo-600 transition-all" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-700">
                        <x-button variant="secondary" :href="route('employee.leave.index')" class="w-full justify-center">Manage Leave</x-button>
                    </div>
                @else
                    <x-empty-state message="No leave balances set" />
                @endif
            </x-card>
        </div>
    </div>

    <div class="mt-8">
        <x-card title="Recent Leave Requests">
            @if($recent_leave_requests->count() > 0)
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($recent_leave_requests as $request)
                        <li class="flex items-center justify-between py-3">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ ucfirst($request->leave_type) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $request->start_date->format('M d') }} &mdash; {{ $request->end_date->format('M d, Y') }}</p>
                            </div>
                            <x-status-badge :status="$request->status" />
                        </li>
                    @endforeach
                </ul>
            @else
                <x-empty-state message="No leave requests yet" icon='<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />' />
            @endif
        </x-card>
    </div>
@endsection
