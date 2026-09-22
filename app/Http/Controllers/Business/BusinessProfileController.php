<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessProfileRequest;
use App\Models\Business;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BusinessProfileController extends Controller
{
    public function show(Business $business): View
    {
        $this->authorize('view', $business);

        $business->load(['profile', 'sector']);

        return view('business.profile.show', compact('business'));
    }

    public function edit(Business $business): View
    {
        $this->authorize('manageProfile', $business);

        $business->load('profile');

        return view('business.profile.edit', compact('business'));
    }

    public function store(BusinessProfileRequest $request, Business $business): RedirectResponse
    {
        $profile = $business->profile()->updateOrCreate(
            ['business_id' => $business->id],
            $request->validated() + [
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'admin_note' => null,
            ],
        );

        AuditService::logAction(
            action: 'business_profile.submitted',
            description: "Public directory listing submitted for '{$business->business_name}'",
            auditable: $profile,
        );

        return redirect()->route('business-owner.businesses.profile.show', $business)
            ->with('success', 'Public directory listing submitted for admin review.');
    }
}
