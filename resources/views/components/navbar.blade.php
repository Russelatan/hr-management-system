<nav class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('employee.dashboard') }}" class="flex items-center gap-2 text-xl font-bold text-indigo-600">
                    <img src="{{ asset('images/logo/aclc.svg') }}" alt="ACLC Logo" class="h-8 w-auto">
                    <span>HR Management</span>
                </a>
            </div>
        </div>
    </div>
</nav>
