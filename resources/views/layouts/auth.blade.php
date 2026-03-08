<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HR Management System')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 font-sans antialiased dark:bg-gray-900">
    {{-- Dark mode toggle --}}
    <div class="fixed right-4 top-4 z-50">
        <button @click="darkMode = !darkMode" class="rounded-lg bg-white p-2 text-gray-500 shadow-md transition-colors hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-200" title="Toggle dark mode">
            <svg x-show="darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" x-cloak>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>
    </div>

    <div class="flex min-h-screen">
        {{-- Left brand panel (hidden on mobile) --}}
        <div class="hidden w-1/2 items-center justify-center bg-gradient-to-br from-indigo-600 to-purple-700 lg:flex">
            <div class="max-w-md px-8 text-center text-white">
                <img src="{{ asset('images/logo/aclc.svg') }}" alt="ACLC Logo" class="mx-auto mb-8 h-20 w-auto brightness-0 invert">
                <h1 class="text-4xl font-bold">HR Management System</h1>
                <p class="mt-4 text-lg text-indigo-100">Streamline your workforce management with our comprehensive HR platform.</p>
            </div>
        </div>

        {{-- Right form panel --}}
        <div class="flex w-full items-center justify-center px-4 py-12 sm:px-6 lg:w-1/2 lg:px-8">
            <div class="w-full max-w-md">
                {{-- Mobile logo --}}
                <div class="mb-8 text-center lg:hidden">
                    <img src="{{ asset('images/logo/aclc.svg') }}" alt="ACLC Logo" class="mx-auto h-14 w-auto">
                    <h2 class="mt-4 text-2xl font-bold text-gray-900 dark:text-gray-100">HR Management</h2>
                </div>

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
