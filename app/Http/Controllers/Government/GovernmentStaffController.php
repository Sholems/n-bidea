<?php

namespace App\Http\Controllers\Government;

use App\Http\Controllers\Controller;
use App\Models\StaffMember;
use App\Services\AuditService;
use Illuminate\View\View;

class GovernmentStaffController extends Controller
{
    public function show(StaffMember $staffMember): View
    {
        $this->authorize('view', $staffMember);

        $staffMember->load(['business', 'documents.documentType']);

        AuditService::logAction(
            action: 'government_view_staff',
            description: "Viewed staff member: {$staffMember->full_name}",
            auditable: $staffMember,
        );

        return view('government.staff.show', compact('staffMember'));
    }
}
