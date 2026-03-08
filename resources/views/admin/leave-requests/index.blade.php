@extends('layouts.app')

@section('title', 'Leave Requests')

@section('content')
    <x-page-header title="Leave Requests" description="Review and manage employee leave requests" />

    <div class="mb-6 flex flex-wrap gap-2">
        @php
            $currentStatus = request('status', 'all');
            $tabs = ['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'];
        @endphp
        @foreach($tabs as $key => $label)
            <a href="{{ route('admin.leave-requests.index', ['status' => $key]) }}"
               class="rounded-lg px-4 py-2 text-sm font-medium transition-colors {{ $currentStatus == $key ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <x-data-table>
        <x-slot:head>
            <th class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:pl-6">Employee</th>
            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Type</th>
            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Start</th>
            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">End</th>
            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Duration</th>
            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
            <th class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
        </x-slot:head>

        @forelse($leaveRequests as $request)
            <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-6">{{ $request->user->name }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ ucfirst(str_replace('-', ' ', $request->leave_type)) }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $request->start_date->format('M d, Y') }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $request->end_date->format('M d, Y') }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                    @if($request->hours_requested)
                        {{ $request->hours_requested }} hrs
                    @else
                        {{ $request->days_requested }} day(s)
                    @endif
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm"><x-status-badge :status="$request->status" /></td>
                <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm sm:pr-6">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.leave-requests.show', $request) }}" class="rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-indigo-600 dark:hover:bg-gray-700 dark:hover:text-indigo-400" title="View">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        @if($request->status === 'pending')
                            <form action="{{ route('admin.leave-requests.approve', $request) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-green-50 hover:text-green-600 dark:hover:bg-green-900/20 dark:hover:text-green-400" title="Approve">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </form>
                            <form action="{{ route('admin.leave-requests.reject', $request) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20 dark:hover:text-red-400" title="Reject">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                        @endif
                    </div>
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
