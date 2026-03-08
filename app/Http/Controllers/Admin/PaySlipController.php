<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaySlipRequest;
use App\Models\PaySlip;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaySlipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paySlips = PaySlip::with(['user', 'creator'])
            ->latest()
            ->paginate(15);

        return view('admin.pay-slips.index', compact('paySlips'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = User::where('role', 'employee')
            ->where('employment_status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.pay-slips.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaySlipRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();

        // Auto-calculate net salary
        $grossSalary = $validated['gross_salary'];
        $deductions = $validated['deductions'] ?? 0;
        $netSalary = $grossSalary - $deductions;

        // Validate that deductions don't exceed gross salary
        if ($deductions > $grossSalary) {
            return back()
                ->withInput()
                ->withErrors(['deductions' => 'Deductions cannot exceed gross salary.']);
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('pay-slips', 'local');
        }

        PaySlip::create([
            'user_id' => $validated['user_id'],
            'month' => $validated['month'],
            'year' => $validated['year'],
            'gross_salary' => $grossSalary,
            'deductions' => $deductions,
            'net_salary' => $netSalary,
            'file_path' => $filePath,
            'distributed_at' => now(),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.pay-slips.index')
            ->with('success', 'Pay slip created and distributed successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PaySlip $pay_slip)
    {
        $paySlip = $pay_slip->load(['user', 'creator']);

        return view('admin.pay-slips.show', compact('paySlip'));
    }

    /**
     * Download pay slip PDF.
     */
    public function download(string $pay_slip)
    {
        $paySlip = PaySlip::with('user')->findOrFail($pay_slip);

        if (! $paySlip->file_path || ! Storage::disk('local')->exists($paySlip->file_path)) {
            abort(404, 'Pay slip file not found.');
        }

        $fileName = "pay-slip-{$paySlip->user->name}-{$paySlip->month}-{$paySlip->year}.pdf";
        $fileName = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $fileName);

        $filePath = Storage::disk('local')->path($paySlip->file_path);

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaySlip $pay_slip)
    {
        $paySlip = $pay_slip;

        if ($paySlip->file_path && Storage::disk('local')->exists($paySlip->file_path)) {
            Storage::disk('local')->delete($paySlip->file_path);
        }

        $paySlip->delete();

        return redirect()->route('admin.pay-slips.index')
            ->with('success', 'Pay slip deleted successfully.');
    }
}
