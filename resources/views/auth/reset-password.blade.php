@extends('layouts.auth')
@section('title', 'Reset Password - HR Management System')

@section('content')
    <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-xl dark:border-gray-700 dark:bg-gray-800">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Reset password</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter your new password below.</p>
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

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <x-form-input label="Email address" name="email" type="email" :required="true" :value="old('email', request()->email)" placeholder="you@example.com" />
            <x-form-input label="New Password" name="password" type="password" :required="true" placeholder="Enter new password" />
            <x-form-input label="Confirm Password" name="password_confirmation" type="password" :required="true" placeholder="Confirm new password" />

            <x-button variant="primary" class="w-full">Reset Password</x-button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                &larr; Back to sign in
            </a>
        </p>
    </div>
@endsection
