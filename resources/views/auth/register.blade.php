@extends('layouts.app')
@section('title', 'Register New User - HR Management System')

@section('content')
    <x-page-header title="Register New User" description="Create a new user account in the system.">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('admin.employees.index')">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Employees
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card>
        <form method="POST" action="{{ route('admin.register') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-form-input label="Full Name" name="name" :required="true" placeholder="John Doe" />
                <x-form-input label="Email" name="email" type="email" :required="true" placeholder="john@example.com" />
                <x-form-input label="Password" name="password" type="password" :required="true" />
                <x-form-input label="Confirm Password" name="password_confirmation" type="password" :required="true" />

                <x-form-select label="Role" name="role" :required="true" :options="['admin' => 'Admin', 'employee' => 'Employee']" />

                <x-form-input label="Employee ID" name="employee_id" placeholder="EMP-001" />
                <x-form-input label="Phone" name="phone" placeholder="+1 234 567 890" />
                <x-form-input label="Date of Birth" name="date_of_birth" type="date" />
                <x-form-input label="Hire Date" name="hire_date" type="date" />

                <x-form-select label="Employment Status" name="employment_status" :options="['active' => 'Active', 'on_leave' => 'On Leave', 'terminated' => 'Terminated', 'suspended' => 'Suspended']" :selected="old('employment_status', 'active')" />
            </div>

            <x-form-textarea label="Address" name="address" placeholder="Enter full address" />

            <div class="flex justify-end">
                <x-button variant="primary">Register User</x-button>
            </div>
        </form>
    </x-card>
@endsection
