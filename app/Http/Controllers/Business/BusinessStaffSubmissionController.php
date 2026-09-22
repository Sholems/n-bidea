<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\CorrectionResponseRequest;
use App\Models\StaffDocumentType;
use App\Models\StaffMember;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;

class BusinessStaffSubmissionController extends Controller
{
    public function submit(StaffMember $staffMember): RedirectResponse
    {
        $this->authorize('update', $staffMember);

        if ($staffMember->status !== 'draft') {
            return back()->with('error', 'Only draft staff records can be submitted for review.');
        }

        if (! in_array($staffMember->business->status, ['approved', 'verified'], true)) {
            return back()->with('error', 'The business must be approved before staff can be submitted.');
        }

        $requiredTypes = StaffDocumentType::where('status', 'active')->where('is_required', true)->pluck('id');
        $uploadedTypeIds = $staffMember->documents()->pluck('staff_document_type_id');

        if ($requiredTypes->diff($uploadedTypeIds)->isNotEmpty()) {
            return back()->with('error', 'Please upload all required documents before submitting.');
        }

        $staffMember->update(['status' => 'submitted']);

        AuditService::logAction(
            action: 'staff.submitted',
            description: "Staff member '{$staffMember->full_name}' submitted for review",
            auditable: $staffMember,
        );

        return back()->with('success', 'Staff member submitted for review successfully.');
    }

    public function correctionResponse(CorrectionResponseRequest $request, StaffMember $staffMember): RedirectResponse
    {
        $this->authorize('update', $staffMember);

        if ($staffMember->status !== 'correction_required') {
            return back()->with('error', 'This staff member is not awaiting corrections.');
        }

        $staffMember->update([
            'correction_response' => $request->validated('note'),
            'status' => 'submitted',
        ]);

        AuditService::logAction(
            action: 'staff.correction_responded',
            description: "Correction response submitted for staff member '{$staffMember->full_name}'",
            auditable: $staffMember,
        );

        return back()->with('success', 'Correction response submitted successfully.');
    }
}
