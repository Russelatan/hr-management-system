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
            'morning_in' => ['nullable', 'date_format:H:i'],
            'morning_out' => ['nullable', 'date_format:H:i', 'after:morning_in'],
            'afternoon_in' => ['nullable', 'date_format:H:i'],
            'afternoon_out' => ['nullable', 'date_format:H:i', 'after:afternoon_in'],
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
            'morning_in.date_format' => 'Morning in time must be in HH:MM format.',
            'morning_out.date_format' => 'Morning out time must be in HH:MM format.',
            'morning_out.after' => 'Morning out time must be after morning in time.',
            'afternoon_in.date_format' => 'Afternoon in time must be in HH:MM format.',
            'afternoon_out.date_format' => 'Afternoon out time must be in HH:MM format.',
            'afternoon_out.after' => 'Afternoon out time must be after afternoon in time.',
            'status.required' => 'The attendance status is required.',
            'status.in' => 'The selected status is invalid.',
        ];
    }
}
