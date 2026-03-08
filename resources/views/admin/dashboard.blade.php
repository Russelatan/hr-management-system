@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <x-page-header title="Admin Dashboard" description="Overview of your HR management system" />

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Total Employees" :value="$stats['total_employees']" color="indigo" icon='<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />' />
        <x-stat-card label="Active Employees" :value="$stats['active_employees']" color="green" icon='<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />' />
        <x-stat-card label="Pending Leave Requests" :value="$stats['pending_leave_requests']" color="yellow" icon='<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />' />
        <x-stat-card label="Pay Slips This Month" :value="$stats['recent_pay_slips']" color="blue" icon='<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />' />
    </div>

    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
        <x-card title="Recent Leave Requests">
            @if($recent_leave_requests->count() > 0)
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($recent_leave_requests as $request)
                        <li class="flex items-center justify-between py-3">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $request->user->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($request->leave_type) }} &mdash; {{ $request->days_requested }} days</p>
                            </div>
                            <x-status-badge :status="$request->status" />
                        </li>
                    @endforeach
                </ul>
                <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-700">
                    <x-button variant="secondary" :href="route('admin.leave-requests.index')" class="w-full justify-center">View all leave requests</x-button>
                </div>
            @else
                <x-empty-state message="No pending leave requests" icon='<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />' />
            @endif
        </x-card>

        <x-card title="Recent Employees">
            @if($recent_employees->count() > 0)
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($recent_employees as $employee)
                        <li class="flex items-center justify-between py-3">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $employee->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $employee->email }}</p>
                            </div>
                            <x-status-badge :status="$employee->employment_status" />
                        </li>
                    @endforeach
                </ul>
                <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-700">
                    <x-button variant="secondary" :href="route('admin.employees.index')" class="w-full justify-center">View all employees</x-button>
                </div>
            @else
                <x-empty-state message="No employees yet" icon='<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />' />
            @endif
        </x-card>
    </div>
@endsection
