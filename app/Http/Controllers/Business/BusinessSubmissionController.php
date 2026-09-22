<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\CorrectionResponseRequest;
use App\Models\Business;
use App\Models\DocumentType;
use App\Services\AuditService;
use App\Services\FeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class BusinessSubmissionController extends Controller
{
    public function submit(Business $business): RedirectResponse
    {
        $this->authorize('update', $business);

        if ($business->status !== 'draft') {
            return back()->with('error', 'Only draft businesses can be submitted for review.');
        }

        $requiredTypes = DocumentType::where('is_required', true)->pluck('id');
        $uploadedTypeIds = $business->documents()->pluck('document_type_id');

        $missing = $requiredTypes->diff($uploadedTypeIds);

        if ($missing->isNotEmpty()) {
            return back()->with('error', 'Please upload all required documents before submitting.');
        }

        DB::transaction(function () use ($business): void {
            $business->update(['status' => 'submitted']);

            FeeService::issue($business, 'certification');
        });

        AuditService::logAction(
            action: 'business.submitted',
            description: "Business '{$business->business_name}' submitted for review",
            auditable: $business,
        );

        return back()->with('success', 'Business submitted for review successfully.');
    }

    public function correctionResponse(CorrectionResponseRequest $request, Business $business): RedirectResponse
    {
        $this->authorize('update', $business);

        if ($business->status !== 'correction_required') {
            return back()->with('error', 'This business is not awaiting corrections.');
        }

        $business->update([
            'correction_response' => $request->validated('note'),
            'status' => 'submitted',
        ]);

        AuditService::logAction(
            action: 'business.correction_responded',
            description: "Correction response submitted for business '{$business->business_name}'",
            auditable: $business,
        );

        return back()->with('success', 'Correction response submitted successfully.');
    }
}
