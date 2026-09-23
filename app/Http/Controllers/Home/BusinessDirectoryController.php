<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessInquiryRequest;
use App\Models\BusinessInquiry;
use App\Models\BusinessProfile;
use App\Models\Sector;
use App\Services\AuditService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BusinessDirectoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = BusinessProfile::query()
            ->with(['business.sector', 'business.certificate'])
            ->approved()
            ->whereHas('business', function (Builder $query): void {
                $query->whereIn('status', ['approved', 'verified']);
            });

        $search = $request->string('query')->trim()->toString();

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('summary', 'like', "%{$search}%")
                    ->orWhere('services', 'like', "%{$search}%")
                    ->orWhere('operating_locations', 'like', "%{$search}%")
                    ->orWhereHas('business', function (Builder $query) use ($search): void {
                        $query->where('business_name', 'like', "%{$search}%")
                            ->orWhere('trading_name', 'like', "%{$search}%")
                            ->orWhere('registry_number', 'like', "%{$search}%")
                            ->orWhere('state', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%");
                    });
            });
        }

        if ($sectorId = $request->integer('sector_id')) {
            $query->whereHas('business', function (Builder $query) use ($sectorId): void {
                $query->where('sector_id', $sectorId);
            });
        }

        if ($state = $request->string('state')->trim()->toString()) {
            $query->whereHas('business', function (Builder $query) use ($state): void {
                $query->where('state', $state);
            });
        }

        $profiles = $query
            ->withExists(['business as currently_verified' => function (Builder $query): void {
                $query->where('status', 'verified')
                    ->where('verification_expires_at', '>', now());
            }])
            ->orderByDesc('currently_verified')
            ->orderByDesc('business_profiles.approved_at')
            ->orderByDesc('business_profiles.id')
            ->paginate(12)
            ->withQueryString();
        $sectors = Sector::where('status', 'active')->orderBy('name')->get();
        $states = BusinessProfile::query()
            ->approved()
            ->whereHas('business', function (Builder $query): void {
                $query->whereIn('status', ['approved', 'verified']);
            })
            ->join('businesses', 'businesses.id', '=', 'business_profiles.business_id')
            ->whereNotNull('businesses.state')
            ->distinct()
            ->orderBy('businesses.state')
            ->pluck('businesses.state');

        return view('public.directory.index', compact('profiles', 'sectors', 'states'));
    }

    public function show(BusinessProfile $businessProfile): View
    {
        abort_unless($businessProfile->newQuery()->whereKey($businessProfile->getKey())->approved()->exists(), 404);

        $businessProfile->load(['business.sector', 'business.certificate']);

        abort_unless(in_array($businessProfile->business->status, ['approved', 'verified']), 404);

        return view('public.directory.show', ['profile' => $businessProfile]);
    }

    public function logo(BusinessProfile $businessProfile): StreamedResponse
    {
        abort_unless($businessProfile->newQuery()->whereKey($businessProfile->getKey())->approved()->exists(), 404);
        abort_unless(
            $businessProfile->logo_path
                && in_array($businessProfile->business()->value('status'), ['approved', 'verified'], true)
                && Storage::disk('private')->exists($businessProfile->logo_path),
            404
        );

        return Storage::disk('private')->response(
            $businessProfile->logo_path,
            null,
            [
                'Content-Type' => $businessProfile->logo_mime_type ?? 'image/jpeg',
                'Cache-Control' => 'public, max-age=3600',
            ],
        );
    }

    public function storeInquiry(BusinessInquiryRequest $request, BusinessProfile $businessProfile): RedirectResponse
    {
        $user = $request->user();

        $inquiry = BusinessInquiry::create($request->safe()->only([
            'requester_company',
            'requester_phone',
            'interest_type',
            'message',
        ]) + [
            'business_profile_id' => $businessProfile->id,
            'requester_id' => $user->id,
            'requester_name' => $user->name,
            'requester_email' => $user->email,
            'status' => 'pending',
        ]);

        AuditService::logAction(
            action: 'business_inquiry.created',
            description: "Directory inquiry submitted for '{$businessProfile->business->business_name}'",
            auditable: $inquiry,
        );

        return redirect()->route('public.directory.show', $businessProfile)
            ->with('success', 'Your interest request has been submitted to NB-CCI for controlled follow-up.');
    }
}
