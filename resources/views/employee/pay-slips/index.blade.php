@extends('layouts.app')

@section('title', 'My Pay Slips')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">My Pay Slips</h1>
            <p class="mt-2 text-sm text-gray-700">View your pay slip history</p>
        </div>
    </div>

    <div class="mt-8 flow-root">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Period</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Gross Salary</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Deductions</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Net Salary</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Distributed</th>
                                <th class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($paySlips as $paySlip)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $paySlip->month }}/{{ $paySlip->year }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">₱{{ number_format($paySlip->gross_salary, 2) }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">₱{{ number_format($paySlip->deductions, 2) }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900">₱{{ number_format($paySlip->net_salary, 2) }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $paySlip->distributed_at ? $paySlip->distributed_at->format('M d, Y') : 'N/A' }}</td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <a href="{{ route('employee.pay-slips.show', $paySlip) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                        @if($paySlip->file_path)
                                            <a href="{{ route('employee.pay-slips.download', $paySlip) }}" class="text-indigo-600 hover:text-indigo-900">Download</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No pay slips found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $paySlips->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
