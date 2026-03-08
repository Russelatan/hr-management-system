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
                <x-form-input label="Check In Time" name="check_in_time" type="time" :value="$attendanceRecord->check_in_time" />
                <x-form-input label="Check Out Time" name="check_out_time" type="time" :value="$attendanceRecord->check_out_time" />
                <x-form-select label="Status" name="status" :required="true" :options="['present' => 'Present', 'absent' => 'Absent', 'late' => 'Late', 'half_day' => 'Half Day']" :selected="$attendanceRecord->status" />
            </div>

            <x-form-textarea label="Notes" name="notes" :value="$attendanceRecord->notes" />

            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('admin.attendance.index')" type="button">Cancel</x-button>
                <x-button variant="primary">Update Record</x-button>
            </div>
        </form>
    </x-card>
@endsection
