@extends('layouts.app')

@section('title', 'My Leave Requests')

@section('content')
    <x-page-header title="My Leave Requests" description="View and manage your leave requests">
        <x-slot:actions>
            <x-button variant="primary" :href="route('employee.leave.create')">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Request Leave
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if($leaveBalances->count() > 0)
        <x-card class="mb-6">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Leave Balances</h3>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                @foreach($leaveBalances as $balance)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst(str_replace('-', ' ', $balance->leave_type)) }}</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $balance->remaining_days }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">of {{ $balance->total_days }} days</p>
                        @if($balance->hasHoursSupport())
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $balance->remaining_hours }} hrs</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    <x-data-table>
        <x-slot:head>
            <th class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:pl-6">Type</th>
            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Start</th>
            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">End</th>
            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Duration</th>
            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Doc</th>
            <th class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
        </x-slot:head>

        @forelse($leaveRequests as $request)
            <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-6">{{ ucfirst($request->leave_type) }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $request->start_date->format('M d, Y') }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $request->end_date->format('M d, Y') }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $request->days_requested }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm"><x-status-badge :status="$request->status" /></td>
                <td class="whitespace-nowrap px-3 py-4 text-sm">
                    @if($request->hasDocument())
                        <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    @else
                        <span class="text-gray-400 dark:text-gray-500">&mdash;</span>
                    @endif
                </td>
                <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm sm:pr-6">
                    <a href="{{ route('employee.leave.show', $request) }}" class="rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-indigo-600 dark:hover:bg-gray-700 dark:hover:text-indigo-400" title="View">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7">
                    <x-empty-state message="No leave requests found" icon='<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />' />
                </td>
            </tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">
        {{ $leaveRequests->links() }}
    </div>
@endsection
