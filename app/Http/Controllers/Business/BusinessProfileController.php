<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessProfileRequest;
use App\Models\Business;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function logo(Business $business): StreamedResponse
    {
        $this->authorize('view', $business);

        $profile = $business->profile;

        abort_unless(
            $profile?->logo_path && Storage::disk('private')->exists($profile->logo_path),
            404
        );

        return Storage::disk('private')->response(
            $profile->logo_path,
            null,
            [
                'Content-Type' => $profile->logo_mime_type ?? 'image/jpeg',
                'Cache-Control' => 'private, max-age=300',
            ],
        );
    }

    public function store(BusinessProfileRequest $request, Business $business): RedirectResponse
    {
        $profileData = $request->safe()->only([
            'summary',
            'services',
            'operating_locations',
            'trade_interests',
            'certifications',
            'website',
            'contact_preference',
        ]);
        $previousLogoPath = $business->profile?->logo_path;
        $newLogoPath = null;

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $newLogoPath = $logo->store("business-logos/{$business->id}", 'private');
            $profileData['logo_path'] = $newLogoPath;
            $profileData['logo_mime_type'] = $logo->getMimeType();
        }

        try {
            $profile = $business->profile()->updateOrCreate(
                ['business_id' => $business->id],
                $profileData + [
                    'status' => 'pending',
                    'approved_by' => null,
                    'approved_at' => null,
                    'admin_note' => null,
                ],
            );
        } catch (\Throwable $exception) {
            if ($newLogoPath) {
                Storage::disk('private')->delete($newLogoPath);
            }

            throw $exception;
        }

        if ($newLogoPath && $previousLogoPath && $previousLogoPath !== $newLogoPath) {
            Storage::disk('private')->delete($previousLogoPath);
        }

        AuditService::logAction(
            action: 'business_profile.submitted',
            description: "Public directory listing submitted for '{$business->business_name}'",
            auditable: $profile,
        );

        return redirect()->route('business-owner.businesses.profile.show', $business)
            ->with('success', 'Public directory listing submitted for admin review.');
    }
}
