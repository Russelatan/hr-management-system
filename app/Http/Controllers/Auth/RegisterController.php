<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm(): \Illuminate\View\View
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(RegisterUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'employee_id' => $validated['employee_id'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'hire_date' => $validated['hire_date'] ?? null,
            'employment_status' => $validated['employment_status'] ?? 'active',
        ]);

        return redirect()->route('admin.employees.index')
            ->with('success', 'User registered successfully.');
    }
}
