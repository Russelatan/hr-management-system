<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceRequest extends FormRequest
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
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i', 'after:check_in_time'],
            'status' => ['required', 'in:present,absent,late,half_day'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'check_in_time.date_format' => 'Check-in time must be in HH:MM format.',
            'check_out_time.date_format' => 'Check-out time must be in HH:MM format.',
            'check_out_time.after' => 'Check-out time must be after check-in time.',
            'status.required' => 'The attendance status is required.',
            'status.in' => 'The selected status is invalid.',
        ];
    }
}
