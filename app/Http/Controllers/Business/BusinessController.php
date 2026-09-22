<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessStoreRequest;
use App\Http\Requests\BusinessUpdateRequest;
use App\Models\Business;
use App\Models\Sector;
use App\Services\AuditService;
use App\Services\BusinessDocumentRequirementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessController extends Controller
{
    public function __construct(
        private readonly BusinessDocumentRequirementService $documentRequirements,
    ) {}

    public function index(Request $request): View
    {
        $businesses = $request->user()->businesses()->with('sector')->latest()->paginate(15);

        return view('business.index', compact('businesses'));
    }

    public function create(): View
    {
        $sectors = Sector::where('status', 'active')->get();
        $countries = Business::COUNTRIES;

        return view('business.create', compact('sectors', 'countries'));
    }

    public function store(BusinessStoreRequest $request): RedirectResponse
    {
        $business = $request->user()->businesses()->create(
            array_merge($request->validated(), ['status' => 'draft'])
        );

        AuditService::logAction(
            action: 'business.created',
            description: "Business '{$business->business_name}' created",
            auditable: $business,
        );

        return redirect()->route('business-owner.businesses.show', $business)
            ->with('success', 'Business created successfully.');
    }

    public function show(Business $business): View
    {
        $this->authorize('view', $business);

        $business->load(['documents.documentType', 'sector']);

        $documents = $business->documents;
        $requirementSummary = $this->documentRequirements->summary($business);

        return view('business.show', compact('business', 'documents', 'requirementSummary'));
    }

    public function edit(Business $business): View
    {
        $this->authorize('update', $business);

        $sectors = Sector::where('status', 'active')->get();
        $countries = Business::COUNTRIES;

        return view('business.edit', compact('business', 'sectors', 'countries'));
    }

    public function update(BusinessUpdateRequest $request, Business $business): RedirectResponse
    {
        $this->authorize('update', $business);

        $business->update($request->validated());

        AuditService::logAction(
            action: 'business.updated',
            description: "Business '{$business->business_name}' updated",
            auditable: $business,
        );

        return back()->with('success', 'Business updated successfully.');
    }

    public function destroy(Business $business): RedirectResponse
    {
        $this->authorize('delete', $business);

        $businessName = $business->business_name;
        $business->delete();

        AuditService::logAction(
            action: 'business.deleted',
            description: "Business '{$businessName}' deleted",
        );

        return redirect()->route('business-owner.businesses.index')
            ->with('success', 'Business deleted successfully.');
    }
}
