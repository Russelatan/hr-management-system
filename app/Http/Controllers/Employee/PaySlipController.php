<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\PaySlip;
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
        $paySlips = PaySlip::where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('employee.pay-slips.index', compact('paySlips'));
    }

    /**
     * Display the specified resource.
     */
    public function show(PaySlip $pay_slip)
    {
        // Ensure employee can only view their own pay slips
        if ($pay_slip->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $paySlip = $pay_slip->load(['creator']);
        return view('employee.pay-slips.show', compact('paySlip'));
    }

    /**
     * Download pay slip PDF.
     */
    public function download(PaySlip $pay_slip)
    {
        // Ensure employee can only download their own pay slips
        if ($pay_slip->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        if (!$pay_slip->file_path || !Storage::disk('local')->exists($pay_slip->file_path)) {
            abort(404, 'Pay slip file not found.');
        }

        $fileName = "pay-slip-{$pay_slip->month}-{$pay_slip->year}.pdf";
        $filePath = Storage::disk('local')->path($pay_slip->file_path);
        
        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
