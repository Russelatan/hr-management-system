@extends('layouts.app')

@section('title', 'Request Leave')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('employee.leave.index') }}" class="text-indigo-600 hover:text-indigo-900">← Back to Leave Requests</a>
        </div>

        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Request Leave</h1>

        @if($leaveBalances->count() > 0)
            <div class="mb-6 bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Available Leave Balance</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @foreach($leaveBalances as $balance)
                        <div>
                            <div class="text-sm text-gray-500">{{ ucfirst($balance->leave_type) }}</div>
                            <div class="text-2xl font-semibold text-gray-900">{{ $balance->remaining_days }}</div>
                            <div class="text-xs text-gray-500">days remaining</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('employee.leave.store') }}" class="bg-white shadow-sm rounded-lg p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="leave_type" class="block text-sm font-medium text-gray-700">Leave Type *</label>
                    <select name="leave_type" id="leave_type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select Leave Type</option>
                        <option value="sick" {{ old('leave_type') == 'sick' ? 'selected' : '' }}>Sick Leave</option>
                        <option value="vacation" {{ old('leave_type') == 'vacation' ? 'selected' : '' }}>Vacation</option>
                        <option value="personal" {{ old('leave_type') == 'personal' ? 'selected' : '' }}>Personal</option>
                        <option value="other" {{ old('leave_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date *</label>
                    <input type="date" name="start_date" id="start_date" required value="{{ old('start_date') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date *</label>
                    <input type="date" name="end_date" id="end_date" required value="{{ old('end_date') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>

            <div class="mt-6">
                <label for="reason" class="block text-sm font-medium text-gray-700">Reason (Optional)</label>
                <textarea name="reason" id="reason" rows="4"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('reason') }}</textarea>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-3">
                <a href="{{ route('employee.leave.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
