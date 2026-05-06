<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreEmployeeRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'employee_id' => ['nullable', 'string', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'hire_date' => ['nullable', 'date'],
            'employment_status' => ['nullable', 'in:active,on_leave,terminated,suspended'],
            'employment_type' => ['nullable', 'in:full-time,part-time,regular'],
            'basic_salary' => ['nullable', 'numeric', 'min:0'],
            'sss_contribution' => ['nullable', 'numeric', 'min:0'],
            'philhealth_contribution' => ['nullable', 'numeric', 'min:0'],
            'pagibig_contribution' => ['nullable', 'numeric', 'min:0'],
            'other_deductions' => ['nullable', 'numeric', 'min:0'],
            'working_days_per_month' => ['nullable', 'integer', 'min:1', 'max:31'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email address is already registered.',
            'employee_id.unique' => 'This employee ID is already in use.',
            'password.confirmed' => 'The password confirmation does not match.',
            'employment_status.in' => 'The selected employment status is invalid.',
            'employment_type.in' => 'The selected employment type is invalid.',
            'basic_salary.min' => 'Basic salary must be a positive number.',
            'sss_contribution.min' => 'SSS contribution must be a positive number.',
            'philhealth_contribution.min' => 'PhilHealth contribution must be a positive number.',
            'pagibig_contribution.min' => 'Pag-IBIG contribution must be a positive number.',
            'other_deductions.min' => 'Other deductions must be a positive number.',
            'working_days_per_month.min' => 'Working days per month must be at least 1.',
            'working_days_per_month.max' => 'Working days per month cannot exceed 31.',
        ];
    }
}
