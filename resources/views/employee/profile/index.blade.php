@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <x-page-header title="My Profile" description="View and update your personal information" />

    {{-- Main form wraps everything so the avatar input is submitted with the rest --}}
    <form method="POST" action="{{ route('employee.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    {{-- Profile Header Card --}}
    <x-card class="mb-6">
        <div class="flex flex-col items-center gap-6 sm:flex-row sm:items-start">
            {{-- Avatar --}}
            <div class="relative shrink-0" x-data="{ previewing: false, previewUrl: '' }">
                <div class="h-28 w-28 overflow-hidden rounded-full ring-4 ring-white dark:ring-gray-700 shadow-md">
                    @if($user->avatar_path)
                        <img x-show="!previewing" src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}"
                             class="h-full w-full object-cover">
                    @else
                        <div x-show="!previewing" class="flex h-full w-full items-center justify-center bg-indigo-600 text-4xl font-bold text-white">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <img x-show="previewing" :src="previewUrl" alt="Preview" class="h-full w-full object-cover" x-cloak>
                </div>

                <label for="avatar_upload" title="Change photo"
                       class="absolute bottom-0 right-0 flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-indigo-600 text-white shadow-md transition hover:bg-indigo-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <input id="avatar_upload" name="avatar" type="file" accept="image/*" class="hidden"
                           @change="previewing = true; previewUrl = URL.createObjectURL($event.target.files[0])">
                </label>
            </div>

            {{-- Info --}}
            <div class="flex-1 text-center sm:text-left">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $user->name }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $user->employee_id ?? 'No Employee ID' }}</p>

                <div class="mt-3 flex flex-wrap justify-center gap-3 sm:justify-start">
                    <x-status-badge :status="$user->employment_status ?? 'active'" />

                    @if($user->employment_type)
                        <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                            {{ ucwords(str_replace('-', ' ', $user->employment_type)) }}
                        </span>
                    @endif
                </div>

                <div class="mt-4 flex flex-wrap justify-center gap-6 sm:justify-start">
                    @if($user->hire_date)
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Hired</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $user->hire_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Years of Service</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $user->yearsOfService() }} yr{{ $user->yearsOfService() !== 1 ? 's' : '' }}</p>
                        </div>
                    @endif

                    @if($leaveBalances->count() > 0)
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Leave Balance</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $leaveBalances->sum('remaining_days') }} days remaining
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </x-card>

        {{-- Personal Information --}}
        <x-card title="Personal Information">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-form-input label="Full Name" name="name" :required="true" :value="$user->name" />
                <x-form-input label="Email Address" name="email" type="email" :required="true" :value="$user->email" />
                <x-form-input label="Phone" name="phone" :value="$user->phone" placeholder="+63 900 000 0000" />
                <x-form-input label="Date of Birth" name="date_of_birth" type="date"
                              :value="$user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : ''" />
            </div>

            <div class="mt-6">
                <x-form-textarea label="Address" name="address" :value="$user->address" :rows="3" />
            </div>

            @error('avatar')
                <p class="mt-4 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            <div class="mt-6 flex items-center justify-end">
                <x-button variant="primary">Save Changes</x-button>
            </div>
        </x-card>

        {{-- Employment Details (read-only) --}}
        <x-card title="Employment Details">
            <dl class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Employee ID</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $user->employee_id ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Employment Status</dt>
                    <dd class="mt-1"><x-status-badge :status="$user->employment_status ?? 'active'" /></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Employment Type</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $user->employment_type ? ucwords(str_replace('-', ' ', $user->employment_type)) : 'N/A' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Hire Date</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $user->hire_date ? $user->hire_date->format('M d, Y') : 'N/A' }}
                    </dd>
                </div>
            </dl>
            <p class="mt-4 text-xs text-gray-400 dark:text-gray-500">Employment details are managed by your HR administrator.</p>
        </x-card>

        {{-- Compensation & Rates --}}
        <x-card title="Compensation & Rates">
            <div class="grid grid-cols-1 gap-x-8 gap-y-6 md:grid-cols-2">
                {{-- Pay Rates --}}
                <div>
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Pay Rates</h3>
                    <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                        <div class="flex items-center justify-between py-2">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Basic Monthly Salary</dt>
                            <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                &#8369;{{ number_format((float) $user->basic_salary, 2) }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Working Days per Month</dt>
                            <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $user->working_days_per_month ?? 22 }} days
                            </dd>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Daily Rate</dt>
                            <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                &#8369;{{ number_format($user->dailyRate(), 2) }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Half-Day Rate</dt>
                            <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                &#8369;{{ number_format($user->halfDayRate(), 2) }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Hourly Rate <span class="text-xs text-gray-400 dark:text-gray-500">(8 hr/day)</span></dt>
                            <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                &#8369;{{ number_format($user->hourlyRate(), 2) }}
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Monthly Deductions --}}
                <div>
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Monthly Deductions</h3>
                    <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                        <div class="flex items-center justify-between py-2">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">SSS</dt>
                            <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                &#8369;{{ number_format((float) $user->sss_contribution, 2) }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">PhilHealth</dt>
                            <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                &#8369;{{ number_format((float) $user->philhealth_contribution, 2) }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Pag-IBIG</dt>
                            <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                &#8369;{{ number_format((float) $user->pagibig_contribution, 2) }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Other Deductions</dt>
                            <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                &#8369;{{ number_format((float) $user->other_deductions, 2) }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <dt class="text-sm font-semibold text-gray-700 dark:text-gray-300">Total Monthly Deductions</dt>
                            <dd class="text-sm font-bold text-red-600 dark:text-red-400">
                                &#8369;{{ number_format($user->totalMonthlyStatutoryDeductions(), 2) }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
            <p class="mt-6 text-xs text-gray-400 dark:text-gray-500">Compensation values are managed by your HR administrator.</p>
        </x-card>

        {{-- Change Password --}}
        <x-card title="Change Password">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-form-input label="New Password" name="password" type="password"
                              placeholder="Leave blank to keep current password" />
                <x-form-input label="Confirm New Password" name="password_confirmation" type="password"
                              placeholder="Repeat new password" />
            </div>
            <div class="mt-6 flex items-center justify-end">
                <x-button variant="secondary">Update Password</x-button>
            </div>
        </x-card>
    </form>
@endsection
