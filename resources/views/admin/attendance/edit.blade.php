@extends('layouts.app')

@section('title', 'Edit Attendance Record')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.attendance.index') }}" class="text-indigo-600 hover:text-indigo-900">← Back to Attendance</a>
        </div>

        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Edit Attendance Record</h1>

        <form method="POST" action="{{ route('admin.attendance.update', $attendanceRecord) }}" class="bg-white shadow-sm rounded-lg p-6">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Employee</label>
                <p class="mt-1 text-sm text-gray-900">{{ $attendanceRecord->user->name }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Date</label>
                <p class="mt-1 text-sm text-gray-900">{{ $attendanceRecord->date->format('M d, Y') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="check_in_time" class="block text-sm font-medium text-gray-700">Check In Time</label>
                    <input type="time" name="check_in_time" id="check_in_time" value="{{ old('check_in_time', $attendanceRecord->check_in_time) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="check_out_time" class="block text-sm font-medium text-gray-700">Check Out Time</label>
                    <input type="time" name="check_out_time" id="check_out_time" value="{{ old('check_out_time', $attendanceRecord->check_out_time) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                    <select name="status" id="status" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="present" {{ old('status', $attendanceRecord->status) == 'present' ? 'selected' : '' }}>Present</option>
                        <option value="absent" {{ old('status', $attendanceRecord->status) == 'absent' ? 'selected' : '' }}>Absent</option>
                        <option value="late" {{ old('status', $attendanceRecord->status) == 'late' ? 'selected' : '' }}>Late</option>
                        <option value="half_day" {{ old('status', $attendanceRecord->status) == 'half_day' ? 'selected' : '' }}>Half Day</option>
                    </select>
                </div>
            </div>

            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" id="notes" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes', $attendanceRecord->notes) }}</textarea>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-3">
                <a href="{{ route('admin.attendance.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Update Record
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
