@extends('layouts.app')

@section('title', 'Request Leave')

@section('content')
    <x-page-header title="Request Leave">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('employee.leave.index')">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if($leaveBalances->count() > 0)
        <x-card class="mb-6">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Available Leave Balance</h3>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                @foreach($leaveBalances as $balance)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst(str_replace('-', ' ', $balance->leave_type)) }}</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $balance->remaining_days }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">days remaining</p>
                        @if($balance->hasHoursSupport())
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $balance->remaining_hours }} hrs</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    <x-card>
        <form method="POST" action="{{ route('employee.leave.store') }}" enctype="multipart/form-data" class="space-y-6"
              x-data="{ leaveType: '{{ old('leave_type', '') }}', hoursTypes: ['sick', 'vacation'], maternityType: 'maternity-leave' }">
            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <x-form-select label="Leave Type" name="leave_type" :required="true" x-model="leaveType"
                        :options="['sick' => 'Sick Leave', 'vacation' => 'Vacation', 'personal' => 'Personal', 'maternity-leave' => 'Maternity Leave', 'paternity-leave' => 'Paternity Leave', 'bereavement-leave' => 'Bereavement Leave', 'other' => 'Other']" />
                </div>

                <x-form-input label="Start Date" name="start_date" type="date" :required="true" />
                <x-form-input label="End Date" name="end_date" type="date" :required="true" />

                <div x-show="hoursTypes.includes(leaveType)" x-transition x-cloak>
                    <x-form-input label="Hours (optional, for partial day)" name="hours_requested" type="number" placeholder="e.g., 4 for half day" />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank for full days. Max 8 hours.</p>
                </div>

                <div x-show="leaveType === maternityType" x-transition x-cloak class="md:col-span-2">
                    <label for="document" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Supporting Document
                        <span x-show="leaveType === maternityType" class="text-red-500">*</span>
                    </label>
                    <input type="file" name="document" id="document" accept=".pdf,.jpg,.jpeg,.png"
                           :required="leaveType === maternityType"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 dark:text-gray-400 dark:file:bg-indigo-900/30 dark:file:text-indigo-400">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Accepted: PDF, JPG, PNG. Max 5MB.</p>
                </div>
            </div>

            <x-form-textarea label="Reason" name="reason" :required="true" :rows="4" />

            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('employee.leave.index')" type="button">Cancel</x-button>
                <x-button variant="primary">Submit Request</x-button>
            </div>
        </form>
    </x-card>
@endsection
