<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Support\Carbon;

class PaySlipComputationService
{
    /**
     * Compute pay slip values for an employee for a given month and year.
     *
     * @return array{
     *     basic_salary: float,
     *     working_days_per_month: int,
     *     daily_rate: float,
     *     total_attendance_records: int,
     *     absent_days: int,
     *     absent_deduction: float,
     *     half_days: int,
     *     half_day_deduction: float,
     *     gross_salary: float,
     *     sss_contribution: float,
     *     philhealth_contribution: float,
     *     pagibig_contribution: float,
     *     other_deductions: float,
     *     total_deductions: float,
     *     net_salary: float,
     * }
     */
    public function compute(User $employee, int $month, int $year): array
    {
        $basicSalary = (float) ($employee->basic_salary ?? 0);
        $workingDays = (int) ($employee->working_days_per_month ?? 22);
        $dailyRate = $workingDays > 0 ? $basicSalary / $workingDays : 0;

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $attendanceRecords = AttendanceRecord::query()
            ->where('user_id', $employee->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        $absentDays = $attendanceRecords->where('status', 'absent')->count();
        $halfDays = $attendanceRecords->where('status', 'half_day')->count();
        $absentDeduction = round($absentDays * $dailyRate, 2);
        $halfDayDeduction = round($halfDays * $dailyRate * 0.5, 2);
        $grossSalary = round(max(0, $basicSalary - $absentDeduction - $halfDayDeduction), 2);

        $sss = round((float) ($employee->sss_contribution ?? 0), 2);
        $philhealth = round((float) ($employee->philhealth_contribution ?? 0), 2);
        $pagibig = round((float) ($employee->pagibig_contribution ?? 0), 2);
        $otherDeductions = round((float) ($employee->other_deductions ?? 0), 2);
        $totalDeductions = round($sss + $philhealth + $pagibig + $otherDeductions, 2);
        $netSalary = round(max(0, $grossSalary - $totalDeductions), 2);

        return [
            'basic_salary' => $basicSalary,
            'working_days_per_month' => $workingDays,
            'daily_rate' => round($dailyRate, 2),
            'total_attendance_records' => $attendanceRecords->count(),
            'absent_days' => $absentDays,
            'absent_deduction' => $absentDeduction,
            'half_days' => $halfDays,
            'half_day_deduction' => $halfDayDeduction,
            'gross_salary' => $grossSalary,
            'sss_contribution' => $sss,
            'philhealth_contribution' => $philhealth,
            'pagibig_contribution' => $pagibig,
            'other_deductions' => $otherDeductions,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
        ];
    }
}
