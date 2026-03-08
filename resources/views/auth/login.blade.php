@extends('layouts.auth')
@section('title', 'Login - HR Management System')

@section('content')
    <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-xl dark:border-gray-700 dark:bg-gray-800">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Welcome back</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Sign in to your account</p>
        </div>

        @if ($errors->any())
            <x-alert type="error" class="mb-6">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <x-form-input label="Email address" name="email" type="email" :required="true" placeholder="you@example.com" />
            <x-form-input label="Password" name="password" type="password" :required="true" placeholder="Enter your password" />

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                    Forgot password?
                </a>
            </div>

            <x-button variant="primary" class="w-full">Sign in</x-button>
        </form>
    </div>
@endsection
