@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
    <x-page-header title="Edit Employee">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('admin.employees.index')">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card>
        <form method="POST" action="{{ route('admin.employees.update', $employee) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-form-input label="Full Name" name="name" :required="true" :value="$employee->name" />
                <x-form-input label="Email" name="email" type="email" :required="true" :value="$employee->email" />
                <x-form-input label="Password" name="password" type="password" placeholder="Leave blank to keep current" />
                <x-form-input label="Confirm Password" name="password_confirmation" type="password" />

                <div>
                    <x-form-input label="Employee ID" name="employee_id" :value="$employee->employee_id" :disabled="true" />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Employee ID is auto-generated and cannot be changed</p>
                </div>

                <x-form-input label="Phone" name="phone" :value="$employee->phone" />
                <x-form-input label="Date of Birth" name="date_of_birth" type="date" :value="$employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : ''" />
                <x-form-input label="Hire Date" name="hire_date" type="date" :value="$employee->hire_date ? $employee->hire_date->format('Y-m-d') : ''" />
                <x-form-select label="Employment Status" name="employment_status" :required="true" :options="['active' => 'Active', 'on_leave' => 'On Leave', 'terminated' => 'Terminated', 'suspended' => 'Suspended']" :selected="$employee->employment_status" />
                <x-form-select label="Employment Type" name="employment_type" :options="['full-time' => 'Full-Time', 'part-time' => 'Part-Time', 'regular' => 'Regular']" :selected="$employee->employment_type" />
            </div>

            <x-form-textarea label="Address" name="address" :value="$employee->address" />

            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Salary & Contributions</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">These values are used to auto-compute pay slips. Leave blank if not applicable.</p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-form-input label="Basic Salary (Monthly)" name="basic_salary" type="number" step="0.01" :value="old('basic_salary', $employee->basic_salary)" />
                <x-form-input label="Working Days / Month" name="working_days_per_month" type="number" :value="old('working_days_per_month', $employee->working_days_per_month ?? 22)" />
                <x-form-input label="SSS Contribution" name="sss_contribution" type="number" step="0.01" :value="old('sss_contribution', $employee->sss_contribution)" />
                <x-form-input label="PhilHealth Contribution" name="philhealth_contribution" type="number" step="0.01" :value="old('philhealth_contribution', $employee->philhealth_contribution)" />
                <x-form-input label="Pag-IBIG Contribution" name="pagibig_contribution" type="number" step="0.01" :value="old('pagibig_contribution', $employee->pagibig_contribution)" />
                <x-form-input label="Other Deductions" name="other_deductions" type="number" step="0.01" :value="old('other_deductions', $employee->other_deductions ?? 0)" />
            </div>

            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('admin.employees.index')" type="button">Cancel</x-button>
                <x-button variant="primary">Update Employee</x-button>
            </div>
        </form>
    </x-card>
@endsection
