@extends('layouts.app')

@section('title', 'Employee Details')

@section('content')
    <x-page-header title="{{ $employee->name }}">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('admin.employees.index')">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </x-button>
            <x-button variant="primary" :href="route('admin.employees.edit', $employee)">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card>
        <dl class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $employee->email }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Employee ID</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $employee->employee_id ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $employee->phone ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Employment Status</dt>
                <dd class="mt-1"><x-status-badge :status="$employee->employment_status" /></dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Employment Type</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $employee->employment_type ? ucfirst(str_replace('-', ' ', $employee->employment_type)) : 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date of Birth</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $employee->date_of_birth ? $employee->date_of_birth->format('M d, Y') : 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Hire Date</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : 'N/A' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $employee->address ?? 'N/A' }}</dd>
            </div>
        </dl>
    </x-card>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-card title="Recent Pay Slips">
            @if($paySlips->count() > 0)
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($paySlips as $paySlip)
                        <li class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $paySlip->month }}/{{ $paySlip->year }}</span>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">₱{{ number_format($paySlip->net_salary, 2) }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <x-empty-state message="No pay slips yet" />
            @endif
        </x-card>

        <x-card title="Recent Leave Requests">
            @if($leaveRequests->count() > 0)
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($leaveRequests as $request)
                        <li class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($request->leave_type) }}</span>
                            <x-status-badge :status="$request->status" />
                        </li>
                    @endforeach
                </ul>
            @else
                <x-empty-state message="No leave requests yet" />
            @endif
        </x-card>

        <x-card title="Recent Attendance">
            @if($attendanceRecords->count() > 0)
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($attendanceRecords as $record)
                        <li class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $record->date->format('M d') }}</span>
                            <x-status-badge :status="$record->status" />
                        </li>
                    @endforeach
                </ul>
            @else
                <x-empty-state message="No attendance records yet" />
            @endif
        </x-card>
    </div>
@endsection
