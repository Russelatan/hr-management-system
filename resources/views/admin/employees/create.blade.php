@extends('layouts.app')

@section('title', 'Create Employee')

@section('content')
    <x-page-header title="Create New Employee">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('admin.employees.index')">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card>
        <form method="POST" action="{{ route('admin.employees.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-form-input label="Full Name" name="name" :required="true" />
                <x-form-input label="Email" name="email" type="email" :required="true" />
                <x-form-input label="Password" name="password" type="password" :required="true" />
                <x-form-input label="Confirm Password" name="password_confirmation" type="password" :required="true" />

                <div>
                    <x-form-input label="Hire Date" name="hire_date" type="date" />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Employee ID will be auto-generated based on hire date</p>
                </div>

                <x-form-input label="Phone" name="phone" />
                <x-form-input label="Date of Birth" name="date_of_birth" type="date" />
                <x-form-select label="Employment Status" name="employment_status" :options="['active' => 'Active', 'on_leave' => 'On Leave', 'terminated' => 'Terminated', 'suspended' => 'Suspended']" :selected="old('employment_status', 'active')" />
                <x-form-select label="Employment Type" name="employment_type" :required="true" :options="['full-time' => 'Full-Time', 'part-time' => 'Part-Time', 'regular' => 'Regular']" />
            </div>

            <x-form-textarea label="Address" name="address" />

            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('admin.employees.index')" type="button">Cancel</x-button>
                <x-button variant="primary">Create Employee</x-button>
            </div>
        </form>
    </x-card>
@endsection
