<?php

namespace App\Http\Requests\Employee;

use App\Models\LeaveRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeaveRequest extends FormRequest
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
            'leave_type' => ['required', 'in:sick,vacation,personal,other,maternity-leave,paternity-leave,bereavement-leave'],
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) {
                    if ($value === now()->toDateString() && now()->hour >= 8) {
                        $fail('Same-day leave must be submitted before 08:00 AM. Please select a future date.');
                    }
                },
            ],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'hours_requested' => ['nullable', 'integer', 'min:1', 'max:8'],
            'reason' => ['nullable', 'string', 'max:500'],
            'document' => [
                Rule::requiredIf(fn () => in_array($this->leave_type, LeaveRequest::leaveTypesRequiringDocument())),
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'leave_type.required' => 'Please select a leave type.',
            'leave_type.in' => 'The selected leave type is invalid.',
            'start_date.required' => 'A start date is required.',
            'start_date.after_or_equal' => 'The start date must be today or a future date.',
            'end_date.required' => 'An end date is required.',
            'end_date.after_or_equal' => 'The end date must be on or after the start date.',
            'hours_requested.min' => 'Hours requested must be at least 1.',
            'hours_requested.max' => 'Hours requested cannot exceed 8 hours per day.',
            'document.required' => 'A supporting document is required for this leave type.',
            'document.mimes' => 'The document must be a PDF, JPG, JPEG, or PNG file.',
            'document.max' => 'The document may not exceed 5MB.',
        ];
    }
}
