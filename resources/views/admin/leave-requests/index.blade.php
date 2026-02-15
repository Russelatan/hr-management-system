@extends('layouts.app')

@section('title', 'Leave Requests')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Leave Requests</h1>
            <p class="mt-2 text-sm text-gray-700">Review and manage employee leave requests</p>
        </div>
    </div>

    <div class="mt-6">
        <div class="flex space-x-4">
            <a href="{{ route('admin.leave-requests.index', ['status' => 'all']) }}"
               class="px-3 py-2 text-sm font-medium rounded-md {{ request('status', 'all') == 'all' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                All
            </a>
            <a href="{{ route('admin.leave-requests.index', ['status' => 'pending']) }}"
               class="px-3 py-2 text-sm font-medium rounded-md {{ request('status') == 'pending' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                Pending
            </a>
            <a href="{{ route('admin.leave-requests.index', ['status' => 'approved']) }}"
               class="px-3 py-2 text-sm font-medium rounded-md {{ request('status') == 'approved' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                Approved
            </a>
            <a href="{{ route('admin.leave-requests.index', ['status' => 'rejected']) }}"
               class="px-3 py-2 text-sm font-medium rounded-md {{ request('status') == 'rejected' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                Rejected
            </a>
        </div>
    </div>

    <div class="mt-8 flow-root">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Employee</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Leave Type</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Start Date</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">End Date</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Duration</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Doc</th>
                                <th class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($leaveRequests as $request)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $request->user->name }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ ucfirst(str_replace('-', ' ', $request->leave_type)) }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $request->start_date->format('M d, Y') }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $request->end_date->format('M d, Y') }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        @if($request->hours_requested)
                                            {{ $request->hours_requested }} hrs
                                        @else
                                            {{ $request->days_requested }} day(s)
                                        @endif
                                    </td>
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
                                        <a href="{{ route('admin.leave-requests.show', $request) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        @if($request->status === 'pending')
                                            <form action="{{ route('admin.leave-requests.approve', $request) }}" method="POST" class="inline ml-3">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                                            </form>
                                            <form action="{{ route('admin.leave-requests.reject', $request) }}" method="POST" class="inline ml-3">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900">Reject</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">No leave requests found</td>
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
