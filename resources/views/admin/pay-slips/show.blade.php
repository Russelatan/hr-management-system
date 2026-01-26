@extends('layouts.app')

@section('title', 'Pay Slip Details')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.pay-slips.index') }}" class="text-indigo-600 hover:text-indigo-900">← Back to Pay Slips</a>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h1 class="text-2xl font-semibold text-gray-900 mb-6">Pay Slip Details</h1>

                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Employee</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $paySlip->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Period</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $paySlip->month }}/{{ $paySlip->year }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Gross Salary</dt>
                        <dd class="mt-1 text-sm text-gray-900">₱{{ number_format($paySlip->gross_salary, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Deductions</dt>
                        <dd class="mt-1 text-sm text-gray-900">₱{{ number_format($paySlip->deductions, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Net Salary</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">₱{{ number_format($paySlip->net_salary, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Distributed At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $paySlip->distributed_at ? $paySlip->distributed_at->format('M d, Y H:i') : 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $paySlip->creator?->name ?? 'N/A' }}</dd>
                    </div>
                    @if($paySlip->file_path)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">PDF File</dt>
                            <dd class="mt-1">
                                <a href="{{ route('admin.pay-slips.download', $paySlip) }}" class="text-indigo-600 hover:text-indigo-900">
                                    Download PDF
                                </a>
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
