@extends('layouts.app')

@section('title', 'Pay Slip Details')

@section('content')
    <x-page-header title="Pay Slip Details">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('admin.pay-slips.index')">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="space-y-6">
        <x-card>
            <dl class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Employee</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $paySlip->user->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Period</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ date('F', mktime(0, 0, 0, $paySlip->month, 1)) }} {{ $paySlip->year }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Distributed At</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $paySlip->distributed_at ? $paySlip->distributed_at->format('M d, Y H:i') : 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created By</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $paySlip->creator?->name ?? 'N/A' }}</dd>
                </div>
                @if($paySlip->file_path)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">PDF File</dt>
                        <dd class="mt-1">
                            <a href="{{ route('admin.pay-slips.download', $paySlip) }}" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Download PDF
                            </a>
                        </dd>
                    </div>
                @endif
            </dl>
        </x-card>

        @if($paySlip->computation_notes)
            @php $notes = $paySlip->computation_notes; @endphp
            <x-card>
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-gray-100">Salary Computation Breakdown</h3>

                <div class="space-y-3">
                    <div class="flex justify-between border-b border-gray-100 pb-3 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Basic Salary (Monthly)</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">₱{{ number_format($notes['basic_salary'], 2) }}</span>
                    </div>

                    <div class="flex justify-between border-b border-gray-100 pb-3 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            Absent Day Deduction
                            <span class="ml-1 text-xs text-gray-400 dark:text-gray-500">({{ $notes['absent_days'] }} day(s) × ₱{{ number_format($notes['daily_rate'], 2) }})</span>
                        </span>
                        <span class="text-sm font-medium text-red-600 dark:text-red-400">− ₱{{ number_format($notes['absent_deduction'], 2) }}</span>
                    </div>

                    @if(isset($notes['half_days']) && $notes['half_days'] > 0)
                        <div class="flex justify-between border-b border-gray-100 pb-3 dark:border-gray-700">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                Half-Day Deduction
                                <span class="ml-1 text-xs text-gray-400 dark:text-gray-500">({{ $notes['half_days'] }} half-day(s) × ₱{{ number_format($notes['daily_rate'] * 0.5, 2) }})</span>
                            </span>
                            <span class="text-sm font-medium text-red-600 dark:text-red-400">− ₱{{ number_format($notes['half_day_deduction'], 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between border-b border-gray-200 pb-3 dark:border-gray-600">
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Gross Salary</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">₱{{ number_format($notes['gross_salary'], 2) }}</span>
                    </div>

                    @if($notes['sss_contribution'] > 0)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">SSS Contribution</span>
                            <span class="text-sm font-medium text-red-600 dark:text-red-400">− ₱{{ number_format($notes['sss_contribution'], 2) }}</span>
                        </div>
                    @endif

                    @if($notes['philhealth_contribution'] > 0)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">PhilHealth Contribution</span>
                            <span class="text-sm font-medium text-red-600 dark:text-red-400">− ₱{{ number_format($notes['philhealth_contribution'], 2) }}</span>
                        </div>
                    @endif

                    @if($notes['pagibig_contribution'] > 0)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Pag-IBIG Contribution</span>
                            <span class="text-sm font-medium text-red-600 dark:text-red-400">− ₱{{ number_format($notes['pagibig_contribution'], 2) }}</span>
                        </div>
                    @endif

                    @if($notes['other_deductions'] > 0)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Other Deductions</span>
                            <span class="text-sm font-medium text-red-600 dark:text-red-400">− ₱{{ number_format($notes['other_deductions'], 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between border-t border-gray-200 pt-3 dark:border-gray-600">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total Deductions</span>
                        <span class="text-sm font-medium text-red-600 dark:text-red-400">− ₱{{ number_format($notes['total_deductions'], 2) }}</span>
                    </div>

                    <div class="flex justify-between rounded-lg bg-gray-50 px-4 py-3 dark:bg-gray-800">
                        <span class="text-base font-bold text-gray-900 dark:text-gray-100">Net Salary</span>
                        <span class="text-base font-bold text-green-600 dark:text-green-400">₱{{ number_format($notes['net_salary'], 2) }}</span>
                    </div>

                    <p class="text-xs text-gray-400 dark:text-gray-500">
                        Based on {{ $notes['working_days_per_month'] }} working days/month · {{ $notes['total_attendance_records'] }} attendance record(s) found for this period.
                    </p>
                </div>
            </x-card>
        @else
            <x-card>
                <dl class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Gross Salary</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">₱{{ number_format($paySlip->gross_salary, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Deductions</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">₱{{ number_format($paySlip->deductions, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Net Salary</dt>
                        <dd class="mt-1 text-lg font-bold text-gray-900 dark:text-gray-100">₱{{ number_format($paySlip->net_salary, 2) }}</dd>
                    </div>
                </dl>
            </x-card>
        @endif
    </div>
@endsection
