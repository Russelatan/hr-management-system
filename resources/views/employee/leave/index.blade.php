@extends('layouts.app')

@section('title', 'My Leave Requests')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">My Leave Requests</h1>
            <p class="mt-2 text-sm text-gray-700">View and manage your leave requests</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('employee.leave.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                Request Leave
            </a>
        </div>
    </div>

    @if($leaveBalances->count() > 0)
        <div class="mt-6 bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Leave Balances</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @foreach($leaveBalances as $balance)
                    <div>
                        <div class="text-sm text-gray-500">{{ ucfirst($balance->leave_type) }}</div>
                        <div class="text-2xl font-semibold text-gray-900">{{ $balance->remaining_days }}</div>
                        <div class="text-xs text-gray-500">of {{ $balance->total_days }} days</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="mt-8 flow-root">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Leave Type</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Start Date</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">End Date</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Days</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($leaveRequests as $request)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ ucfirst($request->leave_type) }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $request->start_date->format('M d, Y') }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $request->end_date->format('M d, Y') }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $request->days_requested }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($request->status === 'approved') bg-green-100 text-green-800
                                            @elseif($request->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800
                                            @endif">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <a href="{{ route('employee.leave.show', $request) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No leave requests found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $leaveRequests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
