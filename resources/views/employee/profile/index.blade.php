@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">My Profile</h1>

        <form method="POST" action="{{ route('employee.profile.update') }}" class="bg-white shadow-sm rounded-lg p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $user->name) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" name="email" id="email" required value="{{ old('email', $user->email) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password (leave blank to keep current)</label>
                    <input type="password" name="password" id="password"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Employee ID</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $user->employee_id ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Employment Status</label>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($user->employment_status === 'active') bg-green-100 text-green-800
                            @elseif($user->employment_status === 'on_leave') bg-yellow-100 text-yellow-800
                            @elseif($user->employment_status === 'terminated') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($user->employment_status) }}
                        </span>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Hire Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $user->hire_date ? $user->hire_date->format('M d, Y') : 'N/A' }}</p>
                </div>
            </div>

            <div class="mt-6">
                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                <textarea name="address" id="address" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('address', $user->address) }}</textarea>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-3">
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
