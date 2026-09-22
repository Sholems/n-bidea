<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffMemberRequest;
use App\Models\Business;
use App\Models\StaffDocumentType;
use App\Models\StaffMember;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BusinessStaffController extends Controller
{
    public function index(Business $business): View
    {
        $this->authorize('view', $business);

        $staffMembers = $business->staffMembers()
            ->withCount('documents')
            ->latest()
            ->paginate(15);

        return view('business.staff.index', compact('business', 'staffMembers'));
    }

    public function create(Business $business): View
    {
        $this->authorize('create', [StaffMember::class, $business]);

        return view('business.staff.create', compact('business'));
    }

    public function store(StaffMemberRequest $request, Business $business): RedirectResponse
    {
        $this->authorize('create', [StaffMember::class, $business]);

        $staffMember = $business->staffMembers()->create(
            $request->validated() + ['status' => 'draft']
        );

        AuditService::logAction(
            action: 'staff.created',
            description: "Staff member '{$staffMember->full_name}' added to business '{$business->business_name}'",
            auditable: $staffMember,
        );

        return redirect()->route('business-owner.staff.show', $staffMember)
            ->with('success', 'Staff member added. Upload their documents and submit them for review.');
    }

    public function show(StaffMember $staffMember): View
    {
        $this->authorize('view', $staffMember);

        $staffMember->load(['business', 'documents.documentType']);

        $documentTypes = StaffDocumentType::where('status', 'active')->orderBy('name')->get();

        return view('business.staff.show', compact('staffMember', 'documentTypes'));
    }

    public function edit(StaffMember $staffMember): View
    {
        $this->authorize('update', $staffMember);

        $staffMember->load('business');

        return view('business.staff.edit', compact('staffMember'));
    }

    public function update(StaffMemberRequest $request, StaffMember $staffMember): RedirectResponse
    {
        $this->authorize('update', $staffMember);

        $staffMember->update($request->validated());

        AuditService::logAction(
            action: 'staff.updated',
            description: "Staff member '{$staffMember->full_name}' updated",
            auditable: $staffMember,
        );

        return redirect()->route('business-owner.staff.show', $staffMember)
            ->with('success', 'Staff member updated successfully.');
    }

    public function destroy(StaffMember $staffMember): RedirectResponse
    {
        $this->authorize('delete', $staffMember);

        $business = $staffMember->business;
        $name = $staffMember->full_name;
        $filePaths = $staffMember->documents()->pluck('file_path');

        $staffMember->delete();

        Storage::disk('private')->delete($filePaths->all());

        AuditService::logAction(
            action: 'staff.deleted',
            description: "Staff member '{$name}' removed from business '{$business->business_name}'",
            auditable: $business,
        );

        return redirect()->route('business-owner.businesses.staff.index', $business)
            ->with('success', 'Staff member removed.');
    }
}
