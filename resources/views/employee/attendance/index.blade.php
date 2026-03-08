@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')
    <x-page-header title="My Attendance" description="View your attendance records" />

    <x-card class="mb-6">
        <form method="GET" action="{{ route('employee.attendance.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-form-input label="Start Date" name="start_date" type="date" :value="$startDate" />
            <x-form-input label="End Date" name="end_date" type="date" :value="$endDate" />
            <div class="flex items-end">
                <x-button variant="primary" class="w-full">Filter</x-button>
            </div>
        </form>
    </x-card>

    <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <x-stat-card label="Total Days" :value="$stats['total_days']" color="indigo" icon='<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />' />
        <x-stat-card label="Present" :value="$stats['present_days']" color="green" icon='<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />' />
        <x-stat-card label="Absent" :value="$stats['absent_days']" color="red" icon='<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />' />
        <x-stat-card label="Late" :value="$stats['late_days']" color="yellow" icon='<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />' />
    </div>

    <x-data-table>
        <x-slot:head>
            <th class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:pl-6">Date</th>
            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Check In</th>
            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Check Out</th>
            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Notes</th>
        </x-slot:head>

        @forelse($attendanceRecords as $record)
            <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-6">{{ $record->date->format('M d, Y') }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $record->check_in_time ?? 'N/A' }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $record->check_out_time ?? 'N/A' }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm"><x-status-badge :status="$record->status" /></td>
                <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $record->notes ?? 'N/A' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5">
                    <x-empty-state message="No attendance records found" icon='<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />' />
                </td>
            </tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">
        {{ $attendanceRecords->links() }}
    </div>
@endsection
