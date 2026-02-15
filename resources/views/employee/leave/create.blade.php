@extends('layouts.app')

@section('title', 'Request Leave')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('employee.leave.index') }}" class="text-indigo-600 hover:text-indigo-900">← Back to Leave Requests</a>
        </div>

        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Request Leave</h1>

        @if($leaveBalances->count() > 0)
            <div class="mb-6 bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Available Leave Balance</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($leaveBalances as $balance)
                        <div>
                            <div class="text-sm text-gray-500">{{ ucfirst(str_replace('-', ' ', $balance->leave_type)) }}</div>
                            <div class="text-2xl font-semibold text-gray-900">{{ $balance->remaining_days }}</div>
                            <div class="text-xs text-gray-500">days remaining</div>
                            @if($balance->hasHoursSupport())
                                <div class="text-sm text-gray-600 mt-1">{{ $balance->remaining_hours }} hrs</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('employee.leave.store') }}" enctype="multipart/form-data" class="bg-white shadow-sm rounded-lg p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="leave_type" class="block text-sm font-medium text-gray-700">Leave Type *</label>
                    <select name="leave_type" id="leave_type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select Leave Type</option>
                        <option value="sick" {{ old('leave_type') == 'sick' ? 'selected' : '' }}>Sick Leave</option>
                        <option value="vacation" {{ old('leave_type') == 'vacation' ? 'selected' : '' }}>Vacation</option>
                        <option value="personal" {{ old('leave_type') == 'personal' ? 'selected' : '' }}>Personal</option>
                        <option value="maternity-leave" {{ old('leave_type') == 'maternity-leave' ? 'selected' : '' }}>Maternity Leave</option>
                        <option value="paternity-leave" {{ old('leave_type') == 'paternity-leave' ? 'selected' : '' }}>Paternity Leave</option>
                        <option value="bereavement-leave" {{ old('leave_type') == 'bereavement-leave' ? 'selected' : '' }}>Bereavement Leave</option>
                        <option value="other" {{ old('leave_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date *</label>
                    <input type="date" name="start_date" id="start_date" required value="{{ old('start_date') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date *</label>
                    <input type="date" name="end_date" id="end_date" required value="{{ old('end_date') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div id="hours_requested_wrapper" class="hidden">
                    <label for="hours_requested" class="block text-sm font-medium text-gray-700">Hours (optional, for partial day leave)</label>
                    <input type="number" name="hours_requested" id="hours_requested" min="1" max="8" value="{{ old('hours_requested') }}"
                           placeholder="e.g., 4 for half day"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <p class="mt-1 text-sm text-gray-500">Leave blank to use full days. Max 8 hours per day.</p>
                </div>

                <div id="document_wrapper" class="hidden md:col-span-2">
                    <label for="document" class="block text-sm font-medium text-gray-700">Supporting Document <span id="document_required_indicator" class="text-red-600 hidden">*</span></label>
                    <input type="file" name="document" id="document" accept=".pdf,.jpg,.jpeg,.png"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <p class="mt-1 text-sm text-gray-500">Accepted: PDF, JPG, PNG. Max 5MB.</p>
                </div>
            </div>

            <div class="mt-6">
                <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                <textarea name="reason" id="reason" rows="4" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('reason') }}</textarea>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-3">
                <a href="{{ route('employee.leave.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const leaveTypeSelect = document.getElementById('leave_type');
    const hoursWrapper = document.getElementById('hours_requested_wrapper');
    const documentWrapper = document.getElementById('document_wrapper');
    const documentInput = document.getElementById('document');
    const documentRequiredIndicator = document.getElementById('document_required_indicator');

    const hoursTypes = ['sick', 'vacation'];
    const maternityType = 'maternity-leave';

    function updateFieldVisibility() {
        const value = leaveTypeSelect.value;

        if (hoursTypes.includes(value)) {
            hoursWrapper.classList.remove('hidden');
        } else {
            hoursWrapper.classList.add('hidden');
            document.getElementById('hours_requested').value = '';
        }

        if (value === maternityType) {
            documentWrapper.classList.remove('hidden');
            documentInput.required = true;
            documentRequiredIndicator.classList.remove('hidden');
        } else {
            documentWrapper.classList.add('hidden');
            documentInput.required = false;
            documentInput.value = '';
            documentRequiredIndicator.classList.add('hidden');
        }
    }

    leaveTypeSelect.addEventListener('change', updateFieldVisibility);
    updateFieldVisibility();
});
</script>
@endpush
@endsection
