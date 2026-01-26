<nav class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('employee.dashboard') }}" class="text-xl font-bold text-indigo-600">
                    HR Management System
                </a>
            </div>
        </div>
    </div>
</nav>
