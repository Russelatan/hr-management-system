<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'leave_type',
        'total_days',
        'used_days',
        'remaining_days',
        'total_hours',
        'used_hours',
        'remaining_hours',
        'year',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasHoursSupport(): bool
    {
        return in_array($this->leave_type, LeaveRequest::leaveTypesWithHoursSupport());
    }
}
