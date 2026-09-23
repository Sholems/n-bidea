<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessProfileReviewRequest;
use App\Models\BusinessProfile;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminBusinessProfileController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('is-admin-or-super');

        $query = BusinessProfile::query()->with(['business.sector', 'approver'])->latest();

        if ($status = $request->string('status')->trim()->toString()) {
            $query->where('status', $status);
        }

        if ($search = $request->string('search')->trim()->toString()) {
            $query->whereHas('business', function ($query) use ($search): void {
                $query->where('business_name', 'like', "%{$search}%")
                    ->orWhere('registry_number', 'like', "%{$search}%");
            });
        }

        $profiles = $query->paginate(20)->withQueryString();

        return view('admin.business-profiles.index', compact('profiles'));
    }

    public function show(BusinessProfile $businessProfile): View
    {
        $this->authorize('is-admin-or-super');

        $businessProfile->load(['business.sector', 'business.user', 'approver', 'inquiries.requester']);

        return view('admin.business-profiles.show', ['profile' => $businessProfile]);
    }

    public function logo(BusinessProfile $businessProfile): StreamedResponse
    {
        $this->authorize('is-admin-or-super');

        abort_unless(
            $businessProfile->logo_path
                && Storage::disk('private')->exists($businessProfile->logo_path),
            404
        );

        return Storage::disk('private')->response(
            $businessProfile->logo_path,
            null,
            [
                'Content-Type' => $businessProfile->logo_mime_type ?? 'image/jpeg',
                'Cache-Control' => 'private, max-age=300',
            ],
        );
    }

    public function update(BusinessProfileReviewRequest $request, BusinessProfile $businessProfile): RedirectResponse
    {
        $this->authorize('is-admin-or-super');

        $validated = $request->validated();

        DB::transaction(function () use ($businessProfile, $validated, $request): void {
            $locked = BusinessProfile::whereKey($businessProfile->getKey())->lockForUpdate()->firstOrFail();

            abort_if(
                $locked->status === $validated['decision'],
                422,
                'This directory listing has already been reviewed with this decision.'
            );

            $locked->update([
                'status' => $validated['decision'],
                'approved_by' => $validated['decision'] === 'approved' ? $request->user()->id : null,
                'approved_at' => $validated['decision'] === 'approved' ? now() : null,
                'admin_note' => $validated['admin_note'] ?? null,
            ]);

            AuditService::logAction(
                action: "business_profile.{$validated['decision']}",
                description: "Directory profile {$validated['decision']} for '{$locked->business->business_name}'",
                auditable: $locked,
            );
        });

        return redirect()->route('admin.business-profiles.show', $businessProfile)
            ->with('success', 'Directory profile review saved successfully.');
    }
}
