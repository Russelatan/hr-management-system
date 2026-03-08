@props(['role' => 'employee'])

<div class="flex h-full flex-col bg-slate-900 text-white">
    {{-- Logo --}}
    <div class="flex h-16 items-center gap-3 px-6 border-b border-white/10">
        <img src="{{ asset('images/logo/aclc.svg') }}" alt="ACLC Logo" class="h-8 w-auto">
        <span class="text-lg font-semibold tracking-tight">HR System</span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        @if($role === 'admin')
            <x-sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </x-slot:icon>
                Dashboard
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.employees.index')" :active="request()->routeIs('admin.employees.*')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </x-slot:icon>
                Employees
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.pay-slips.index')" :active="request()->routeIs('admin.pay-slips.*')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </x-slot:icon>
                Pay Slips
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.leave-requests.index')" :active="request()->routeIs('admin.leave-requests.*')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </x-slot:icon>
                Leave Requests
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.attendance.index')" :active="request()->routeIs('admin.attendance.*')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </x-slot:icon>
                Attendance
            </x-sidebar-link>
        @else
            <x-sidebar-link :href="route('employee.dashboard')" :active="request()->routeIs('employee.dashboard')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </x-slot:icon>
                Dashboard
            </x-sidebar-link>

            <x-sidebar-link :href="route('employee.pay-slips.index')" :active="request()->routeIs('employee.pay-slips.*')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </x-slot:icon>
                Pay Slips
            </x-sidebar-link>

            <x-sidebar-link :href="route('employee.leave.index')" :active="request()->routeIs('employee.leave.*')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </x-slot:icon>
                Leave
            </x-sidebar-link>

            <x-sidebar-link :href="route('employee.attendance.index')" :active="request()->routeIs('employee.attendance.*')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </x-slot:icon>
                Attendance
            </x-sidebar-link>

            <x-sidebar-link :href="route('employee.profile.index')" :active="request()->routeIs('employee.profile.*')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </x-slot:icon>
                Profile
            </x-sidebar-link>
        @endif
    </nav>

    {{-- User info + Logout --}}
    <div class="border-t border-white/10 px-3 py-4">
        <div class="mb-3 flex items-center gap-3 px-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-600 text-sm font-semibold text-white">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                <p class="text-xs text-slate-400 capitalize">{{ Auth::user()->role }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-slate-300 transition-colors hover:bg-white/10 hover:text-white">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </button>
        </form>
    </div>
</div>
