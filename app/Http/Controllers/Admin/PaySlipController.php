<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaySlip;
use App\Models\User;
use Illuminate\Http\Request;
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'gross_salary' => ['required', 'numeric', 'min:0'],
            'deductions' => ['nullable', 'numeric', 'min:0'],
            'net_salary' => ['required', 'numeric', 'min:0'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('pay-slips', 'local');
        }

        PaySlip::create([
            'user_id' => $validated['user_id'],
            'month' => $validated['month'],
            'year' => $validated['year'],
            'gross_salary' => $validated['gross_salary'],
            'deductions' => $validated['deductions'] ?? 0,
            'net_salary' => $validated['net_salary'],
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
        
        if (!$paySlip->file_path || !Storage::disk('local')->exists($paySlip->file_path)) {
            abort(404, 'Pay slip file not found.');
        }

        $fileName = "pay-slip-{$paySlip->user->name}-{$paySlip->month}-{$paySlip->year}.pdf";
        $fileName = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $fileName);

        $filePath = storage_path('app/' . $paySlip->file_path);
        
        return response()->download($filePath, $fileName);
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
