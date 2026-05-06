<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeaveBalanceRequest extends FormRequest
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
            'total_days' => ['required', 'numeric', 'min:0'],
            'used_days' => ['required', 'numeric', 'min:0'],
            'total_hours' => ['required', 'numeric', 'min:0'],
            'used_hours' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'total_days.required' => 'Total days is required.',
            'total_days.min' => 'Total days cannot be negative.',
            'used_days.required' => 'Used days is required.',
            'used_days.min' => 'Used days cannot be negative.',
            'total_hours.required' => 'Total hours is required.',
            'total_hours.min' => 'Total hours cannot be negative.',
            'used_hours.required' => 'Used hours is required.',
            'used_hours.min' => 'Used hours cannot be negative.',
        ];
    }
}
