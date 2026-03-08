@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <x-page-header title="My Profile" description="View and update your personal information" />

    <x-card>
        <form method="POST" action="{{ route('employee.profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-form-input label="Full Name" name="name" :required="true" :value="$user->name" />
                <x-form-input label="Email" name="email" type="email" :required="true" :value="$user->email" />
                <x-form-input label="Password" name="password" type="password" placeholder="Leave blank to keep current" />
                <x-form-input label="Confirm Password" name="password_confirmation" type="password" />
                <x-form-input label="Phone" name="phone" :value="$user->phone" />

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Employee ID</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->employee_id ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Employment Status</label>
                    <div class="mt-1">
                        <x-status-badge :status="$user->employment_status" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hire Date</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->hire_date ? $user->hire_date->format('M d, Y') : 'N/A' }}</p>
                </div>
            </div>

            <x-form-textarea label="Address" name="address" :value="$user->address" />

            <div class="flex items-center justify-end">
                <x-button variant="primary">Update Profile</x-button>
            </div>
        </form>
    </x-card>
@endsection
