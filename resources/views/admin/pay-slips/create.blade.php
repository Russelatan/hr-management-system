@extends('layouts.app')

@section('title', 'Upload Pay Slip')

@section('content')
    <x-page-header title="Upload Pay Slip">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('admin.pay-slips.index')">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card>
        <form method="POST" action="{{ route('admin.pay-slips.store') }}" enctype="multipart/form-data" class="space-y-6" x-data="paySlipForm()">
            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-form-select label="Employee" name="user_id" :required="true">
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('user_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }} ({{ $employee->email }})
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

                <div>
                    <x-form-input label="Gross Salary" name="gross_salary" type="number" :required="true" x-model="gross" />
                </div>

                <div>
                    <x-form-input label="Deductions" name="deductions" type="number" :value="old('deductions', 0)" x-model="deductions" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Net Salary</label>
                    <div class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm font-semibold dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" :class="net < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100'">
                        ₱<span x-text="netFormatted"></span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Calculated automatically: Gross Salary - Deductions</p>
                </div>

                <div class="md:col-span-2">
                    <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pay Slip PDF (Optional)</label>
                    <input type="file" name="file" id="file" accept=".pdf"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 dark:text-gray-400 dark:file:bg-indigo-900/30 dark:file:text-indigo-400">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maximum file size: 10MB</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <x-button variant="secondary" :href="route('admin.pay-slips.index')" type="button">Cancel</x-button>
                <x-button variant="primary">Upload Pay Slip</x-button>
            </div>
        </form>
    </x-card>
@endsection

@push('scripts')
<script>
    function paySlipForm() {
        return {
            gross: {{ old('gross_salary', 0) }},
            deductions: {{ old('deductions', 0) }},
            get net() { return Math.max(0, (parseFloat(this.gross) || 0) - (parseFloat(this.deductions) || 0)); },
            get netFormatted() { return this.net.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
        }
    }
</script>
@endpush
