@extends('layouts.app')

@section('title', 'Generate Pay Slip')

@section('content')
    <x-page-header title="Generate Pay Slip">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('admin.pay-slips.index')">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card>
        <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
            <div class="flex gap-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p class="text-sm font-medium text-blue-800 dark:text-blue-300">Automatic Computation</p>
                    <p class="mt-1 text-sm text-blue-700 dark:text-blue-400">
                        The pay slip will be computed automatically from the employee's basic salary, attendance records for the selected month, and their stored contribution amounts. Make sure the employee's salary information is set before generating.
                    </p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.pay-slips.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-form-select label="Employee" name="user_id" :required="true">
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('user_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }} ({{ $employee->employee_id ?? $employee->email }})
                            @if(!$employee->basic_salary)
                                — ⚠ No salary set
                            @else
                                — ₱{{ number_format($employee->basic_salary, 2) }}/mo
                            @endif
                        </option>
                    @endforeach
                </x-form-select>

                <x-form-select label="Month" name="month" :required="true">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ old('month', now()->month) == $i ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                        </option>
                    @endfor
                </x-form-select>

                <x-form-input label="Year" name="year" type="number" :required="true" :value="old('year', now()->year)" />

                <div class="md:col-span-2">
                    <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Attach PDF (Optional)</label>
                    <input type="file" name="file" id="file" accept=".pdf"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 dark:text-gray-400 dark:file:bg-indigo-900/30 dark:file:text-indigo-400">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload an official pay slip PDF if available. Maximum file size: 10MB.</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('admin.pay-slips.index')" type="button">Cancel</x-button>
                <x-button variant="primary">Generate Pay Slip</x-button>
            </div>
        </form>
    </x-card>
@endsection
