<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeeUpdateRequest;
use App\Models\Fee;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BusinessFeeController extends Controller
{
    public function index(Request $request): View
    {
        $fees = Fee::whereHas('business', fn ($query) => $query->where('user_id', $request->user()->id))
            ->with('business')
            ->latest()
            ->paginate(15);

        return view('business.fees.index', compact('fees'));
    }

    public function uploadProof(FeeUpdateRequest $request, Fee $fee): RedirectResponse
    {
        $this->authorize('view', $fee->business);

        if (! in_array($fee->payment_status, ['unpaid', 'pending_confirmation'], true)) {
            return back()->with('error', 'This fee has already been settled.');
        }

        $previousPath = $fee->proof_file_path;

        $file = $request->file('proof_file');
        $path = $file->store('payment-proofs', 'private');

        $fee->update([
            'proof_file_path' => $path,
            'payment_reference' => $request->input('payment_reference'),
            'payment_status' => 'pending_confirmation',
        ]);

        if ($previousPath && $previousPath !== $path) {
            Storage::disk('private')->delete($previousPath);
        }

        AuditService::logAction(
            action: 'fee.proof_uploaded',
            description: "Payment proof uploaded for fee #{$fee->id}",
            auditable: $fee,
        );

        return back()->with('success', 'Payment proof uploaded successfully.');
    }
}
