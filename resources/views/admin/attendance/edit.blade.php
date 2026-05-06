@extends('layouts.app')

@section('title', 'Edit Attendance Record')

@section('content')
    <x-page-header title="Edit Attendance Record">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('admin.attendance.index')">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card>
        <form method="POST" action="{{ route('admin.attendance.update', $attendanceRecord) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Employee</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $attendanceRecord->user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $attendanceRecord->date->format('M d, Y') }}</p>
                </div>
                <x-form-select label="Status" name="status" :required="true" :options="['present' => 'Present', 'absent' => 'Absent', 'late' => 'Late', 'half_day' => 'Half Day']" :selected="old('status', $attendanceRecord->status)" />
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Morning Session</h3>
                <div class="mt-3 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <x-form-input label="Morning In" name="morning_in" type="time" :value="old('morning_in', $attendanceRecord->morning_in)" />
                    <x-form-input label="Morning Out" name="morning_out" type="time" :value="old('morning_out', $attendanceRecord->morning_out)" />
                </div>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Afternoon Session</h3>
                <div class="mt-3 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <x-form-input label="Afternoon In" name="afternoon_in" type="time" :value="old('afternoon_in', $attendanceRecord->afternoon_in)" />
                    <x-form-input label="Afternoon Out" name="afternoon_out" type="time" :value="old('afternoon_out', $attendanceRecord->afternoon_out)" />
                </div>
            </div>

            <x-form-textarea label="Notes" name="notes" :value="old('notes', $attendanceRecord->notes)" />

            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('admin.attendance.index')" type="button">Cancel</x-button>
                <x-button variant="primary">Update Record</x-button>
            </div>
        </form>
    </x-card>
@endsection
