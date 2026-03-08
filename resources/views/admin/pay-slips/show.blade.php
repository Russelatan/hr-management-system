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

    <x-card>
        <dl class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Employee</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $paySlip->user->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Period</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $paySlip->month }}/{{ $paySlip->year }}</dd>
            </div>
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
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Distributed At</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $paySlip->distributed_at ? $paySlip->distributed_at->format('M d, Y H:i') : 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created By</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $paySlip->creator?->name ?? 'N/A' }}</dd>
            </div>
            @if($paySlip->file_path)
                <div>
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
@endsection
