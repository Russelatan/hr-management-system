@extends('layouts.app')

@section('title', 'Pay Slips')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Pay Slips</h1>
            <p class="mt-2 text-sm text-gray-700">Manage and distribute pay slips</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('admin.pay-slips.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                Upload Pay Slip
            </a>
        </div>
    </div>

    <div class="mt-8 flow-root">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Employee</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Period</th>
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
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $paySlip->user->name }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $paySlip->month }}/{{ $paySlip->year }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">${{ number_format($paySlip->gross_salary, 2) }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">${{ number_format($paySlip->deductions, 2) }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900">${{ number_format($paySlip->net_salary, 2) }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $paySlip->distributed_at ? $paySlip->distributed_at->format('M d, Y') : 'N/A' }}</td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <a href="{{ route('admin.pay-slips.show', $paySlip) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        <form action="{{ route('admin.pay-slips.destroy', $paySlip) }}" method="POST" class="inline ml-3" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No pay slips found</td>
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
