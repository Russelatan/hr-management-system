@extends('layouts.auth')
@section('title', 'Forgot Password - HR Management System')

@section('content')
    <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-xl dark:border-gray-700 dark:bg-gray-800">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Forgot password?</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter your email and we'll send you a reset link.</p>
        </div>

        @if (session('status'))
            <x-alert type="success" class="mb-6">{{ session('status') }}</x-alert>
        @endif

        @if ($errors->any())
            <x-alert type="error" class="mb-6">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf
            <x-form-input label="Email address" name="email" type="email" :required="true" placeholder="you@example.com" />
            <x-button variant="primary" class="w-full">Send Reset Link</x-button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                &larr; Back to sign in
            </a>
        </p>
    </div>
@endsection
