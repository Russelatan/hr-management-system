@extends('layouts.app')

@section('title', 'Leave Balances')

@section('content')
    <x-page-header title="Leave Balances" description="Manage employee leave allocations by year" />

    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.leave-balances.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="min-w-48">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Employee</label>
                <select name="employee_id" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" @selected($employeeId == $employee->id)>{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Year</label>
                <input type="number" name="year" value="{{ $year }}" min="2000" max="2100"
                       class="mt-1 block w-28 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
            </div>

            <x-button variant="primary" type="submit">Filter</x-button>
        </form>
    </x-card>

    <x-data-table>
        <x-slot:head>
            <th class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:pl-6">Employee</th>
            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Leave Type</th>
            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Year</th>
            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Total Days</th>
            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Used</th>
            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Remaining</th>
            <th class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
        </x-slot:head>

        @forelse($leaveBalances as $balance)
            <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-6">{{ $balance->user->name }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ ucwords(str_replace('-', ' ', $balance->leave_type)) }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $balance->year }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $balance->total_days }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $balance->used_days }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm">
                    <span class="{{ $balance->remaining_days > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-medium">
                        {{ $balance->remaining_days }}
                    </span>
                </td>
                <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm sm:pr-6">
                    <a href="{{ route('admin.leave-balances.edit', $balance) }}"
                       class="rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-indigo-600 dark:hover:bg-gray-700 dark:hover:text-indigo-400" title="Edit">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7">
                    <x-empty-state message="No leave balance records found" icon='<path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />' />
                </td>
            </tr>
        @endforelse
    </x-data-table>

    <div class="mt-4">
        {{ $leaveBalances->links() }}
    </div>
@endsection
