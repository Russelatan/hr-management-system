@extends('layouts.app')

@section('title', 'Edit Leave Balance')

@section('content')
    <x-page-header title="Edit Leave Balance">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('admin.leave-balances.index')">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card>
        <div class="mb-6 grid grid-cols-1 gap-4 rounded-lg bg-gray-50 p-4 dark:bg-gray-700/50 sm:grid-cols-3">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Employee</p>
                <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $leaveBalance->user->name }}</p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Leave Type</p>
                <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ ucwords(str_replace('-', ' ', $leaveBalance->leave_type)) }}</p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Year</p>
                <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $leaveBalance->year }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.leave-balances.update', $leaveBalance) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <x-form-input label="Total Days" name="total_days" type="number" step="0.5" min="0"
                              :value="old('total_days', $leaveBalance->total_days)" :required="true" />
                <x-form-input label="Used Days" name="used_days" type="number" step="0.5" min="0"
                              :value="old('used_days', $leaveBalance->used_days)" :required="true" />
                <x-form-input label="Total Hours" name="total_hours" type="number" step="0.5" min="0"
                              :value="old('total_hours', $leaveBalance->total_hours)" :required="true" />
                <x-form-input label="Used Hours" name="used_hours" type="number" step="0.5" min="0"
                              :value="old('used_hours', $leaveBalance->used_hours)" :required="true" />
            </div>

            <p class="text-xs text-gray-500 dark:text-gray-400">Remaining days and hours are automatically recalculated as Total minus Used.</p>

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-700">
                <x-button variant="secondary" :href="route('admin.leave-balances.index')" type="button">Cancel</x-button>
                <x-button variant="primary">Save Changes</x-button>
            </div>
        </form>
    </x-card>
@endsection
