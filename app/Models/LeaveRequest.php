<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'leave_type',
        'start_date',
        'end_date',
        'days_requested',
        'hours_requested',
        'reason',
        'document_path',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function hasDocument(): bool
    {
        return ! empty($this->document_path);
    }

    public static function leaveTypesWithHoursSupport(): array
    {
        return ['sick', 'vacation'];
    }

    public static function leaveTypesRequiringDocument(): array
    {
        return ['maternity-leave'];
    }
}
