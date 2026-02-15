@extends('layouts.app')

@section('title', 'Create Employee')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Create New Employee</h1>

        <form method="POST" action="{{ route('admin.employees.store') }}" class="bg-white shadow-sm rounded-lg p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" name="email" id="email" required value="{{ old('email') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                    <input type="password" name="password" id="password" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="hire_date" class="block text-sm font-medium text-gray-700">Hire Date</label>
                    <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <p class="mt-1 text-sm text-gray-500">Employee ID will be auto-generated based on hire date (format: EMPYYYYMMDD-NNN)</p>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>


                <div>
                    <label for="employment_status" class="block text-sm font-medium text-gray-700">Employment Status</label>
                    <select name="employment_status" id="employment_status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="active" {{ old('employment_status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="on_leave" {{ old('employment_status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                        <option value="terminated" {{ old('employment_status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                        <option value="suspended" {{ old('employment_status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <div>
                    <label for="employment_type" class="block text-sm font-medium text-gray-700">Employment Type</label>
                    <select name="employment_type" id="employment_type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select Employment Type</option>
                        <option value="full-time" {{ old('employment_type') == 'full-time' ? 'selected' : '' }}>Full-Time</option>
                        <option value="part-time" {{ old('employment_type') == 'part-time' ? 'selected' : '' }}>Part-Time</option>
                        <option value="regular" {{ old('employment_type') == 'regular' ? 'selected' : '' }}>Regular</option>
                    </select>
                </div>
            </div>

            <div class="mt-6">
                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                <textarea name="address" id="address" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('address') }}</textarea>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-3">
                <a href="{{ route('admin.employees.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Create Employee
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
