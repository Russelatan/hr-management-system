<nav class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('employee.dashboard') }}" class="text-xl font-bold text-indigo-600">
                        HR Management System
                    </a>
                </div>
                <!-- Mobile menu button -->
                <div class="sm:hidden ml-4 flex items-center">
                    <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" id="mobile-menu-button" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <!-- Desktop menu -->
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'border-indigo-500' : 'border-transparent' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('admin.employees.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('admin.employees.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                            Employees
                        </a>
                        <a href="{{ route('admin.pay-slips.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('admin.pay-slips.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                            Pay Slips
                        </a>
                        <a href="{{ route('admin.leave-requests.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('admin.leave-requests.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                            Leave Requests
                        </a>
                        <a href="{{ route('admin.attendance.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('admin.attendance.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                            Attendance
                        </a>
                    @else
                        <a href="{{ route('employee.dashboard') }}" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('employee.dashboard') ? 'border-indigo-500' : 'border-transparent' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('employee.pay-slips.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('employee.pay-slips.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                            Pay Slips
                        </a>
                        <a href="{{ route('employee.leave.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('employee.leave.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                            Leave
                        </a>
                        <a href="{{ route('employee.attendance.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('employee.attendance.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                            Attendance
                        </a>
                        <a href="{{ route('employee.profile.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('employee.profile.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                            Profile
                        </a>
                    @endif
                </div>
            </div>
            <div class="flex items-center">
                <span class="text-sm text-gray-700 mr-4 hidden sm:inline">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">Logout</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Mobile menu -->
    <div class="sm:hidden hidden" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="bg-indigo-50 border-indigo-500 text-indigo-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Dashboard
                </a>
                <a href="{{ route('admin.employees.index') }}" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Employees
                </a>
                <a href="{{ route('admin.pay-slips.index') }}" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Pay Slips
                </a>
                <a href="{{ route('admin.leave-requests.index') }}" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Leave Requests
                </a>
                <a href="{{ route('admin.attendance.index') }}" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Attendance
                </a>
            @else
                <a href="{{ route('employee.dashboard') }}" class="bg-indigo-50 border-indigo-500 text-indigo-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Dashboard
                </a>
                <a href="{{ route('employee.pay-slips.index') }}" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Pay Slips
                </a>
                <a href="{{ route('employee.leave.index') }}" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Leave
                </a>
                <a href="{{ route('employee.attendance.index') }}" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Attendance
                </a>
                <a href="{{ route('employee.profile.index') }}" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Profile
                </a>
            @endif
        </div>
        <div class="pt-4 pb-3 border-t border-gray-200">
            <div class="px-4">
                <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
            </div>
        </div>
    </div>
</nav>
