<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property \Carbon\Carbon|null $date_of_birth
 * @property \Carbon\Carbon|null $hire_date
 * @property \Carbon\Carbon|null $email_verified_at
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'employee_id',
        'phone',
        'address',
        'avatar_path',
        'date_of_birth',
        'hire_date',
        'employment_status',
        'employment_type',
        'basic_salary',
        'sss_contribution',
        'philhealth_contribution',
        'pagibig_contribution',
        'other_deductions',
        'working_days_per_month',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'hire_date' => 'date',
            'basic_salary' => 'decimal:2',
            'sss_contribution' => 'decimal:2',
            'philhealth_contribution' => 'decimal:2',
            'pagibig_contribution' => 'decimal:2',
            'other_deductions' => 'decimal:2',
        ];
    }

    public function paySlips()
    {
        return $this->hasMany(PaySlip::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function avatarUrl(): string
    {
        if (! $this->avatar_path) {
            return '';
        }

        $filename = basename($this->avatar_path);

        return route('avatar.show', ['filename' => $filename]);
    }

    public function yearsOfService(): int
    {
        return $this->hire_date
            ? (int) $this->hire_date->diffInYears(now())
            : 0;
    }

    public function dailyRate(): float
    {
        $workingDays = (int) ($this->working_days_per_month ?? 22);

        return $workingDays > 0
            ? round((float) $this->basic_salary / $workingDays, 2)
            : 0.0;
    }

    public function halfDayRate(): float
    {
        return round($this->dailyRate() / 2, 2);
    }

    public function hourlyRate(): float
    {
        return round($this->dailyRate() / 8, 2);
    }

    public function totalMonthlyStatutoryDeductions(): float
    {
        return round(
            (float) $this->sss_contribution
            + (float) $this->philhealth_contribution
            + (float) $this->pagibig_contribution
            + (float) $this->other_deductions,
            2
        );
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }
}
