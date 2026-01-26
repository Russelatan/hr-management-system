@extends('layouts.app')

@section('title', 'Employee Details')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.employees.index') }}" class="text-indigo-600 hover:text-indigo-900">← Back to Employees</a>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">{{ $employee->name }}</h1>
                    <a href="{{ route('admin.employees.edit', $employee) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                </div>

                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Employee ID</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->employee_id ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->phone ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Employment Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($employee->employment_status === 'active') bg-green-100 text-green-800
                                @elseif($employee->employment_status === 'on_leave') bg-yellow-100 text-yellow-800
                                @elseif($employee->employment_status === 'terminated') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($employee->employment_status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->date_of_birth ? $employee->date_of_birth->format('M d, Y') : 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Hire Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : 'N/A' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Address</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->address ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Pay Slips</h3>
                @if($paySlips->count() > 0)
                    <ul class="space-y-2">
                        @foreach($paySlips as $paySlip)
                            <li class="text-sm">
                                <span class="text-gray-900">{{ $paySlip->month }}/{{ $paySlip->year }}</span>
                                <span class="text-gray-500 ml-2">₱{{ number_format($paySlip->net_salary, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500">No pay slips yet</p>
                @endif
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Leave Requests</h3>
                @if($leaveRequests->count() > 0)
                    <ul class="space-y-2">
                        @foreach($leaveRequests as $request)
                            <li class="text-sm">
                                <span class="text-gray-900">{{ ucfirst($request->leave_type) }}</span>
                                <span class="text-gray-500 ml-2">{{ $request->status }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500">No leave requests yet</p>
                @endif
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Attendance</h3>
                @if($attendanceRecords->count() > 0)
                    <ul class="space-y-2">
                        @foreach($attendanceRecords as $record)
                            <li class="text-sm">
                                <span class="text-gray-900">{{ $record->date->format('M d') }}</span>
                                <span class="text-gray-500 ml-2">{{ ucfirst($record->status) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500">No attendance records yet</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
