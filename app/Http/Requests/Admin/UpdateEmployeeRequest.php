<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $employeeId = $this->route('employee');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$employeeId],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'hire_date' => ['nullable', 'date'],
            'employment_status' => ['required', 'in:active,on_leave,terminated,suspended'],
            'employment_type' => ['nullable', 'in:full-time,part-time,regular'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email address is already registered to another account.',
            'password.confirmed' => 'The password confirmation does not match.',
            'employment_status.required' => 'The employment status is required.',
            'employment_status.in' => 'The selected employment status is invalid.',
            'employment_type.in' => 'The selected employment type is invalid.',
        ];
    }
}
