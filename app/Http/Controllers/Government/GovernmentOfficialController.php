<?php

namespace App\Http\Controllers\Government;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\StaffMember;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class GovernmentOfficialController extends Controller
{
    public function search(Request $request): View
    {
        $searchTerm = trim($request->input('query', ''));

        if ($searchTerm === '') {
            return view('government.search', [
                'results' => (new LengthAwarePaginator([], 0, 20))->withQueryString(),
                'staffResults' => (new LengthAwarePaginator([], 0, 20))->withQueryString(),
                'request' => $request,
            ]);
        }

        $request->validate([
            'query' => ['required', 'string', 'max:255'],
        ]);

        $wildcard = "%{$searchTerm}%";

        $results = Business::where(function ($query) use ($wildcard) {
            $query->where('business_name', 'like', $wildcard)
                ->orWhere('cac_number', 'like', $wildcard)
                ->orWhere('nrs_number', 'like', $wildcard)
                ->orWhere('registry_number', 'like', $wildcard)
                ->orWhere('phone', 'like', $wildcard)
                ->orWhere('email', 'like', $wildcard);
        })->with('sector')->latest()->paginate(20)->withQueryString();

        $staffResults = StaffMember::where(function ($query) use ($wildcard) {
            $query->where('full_name', 'like', $wildcard)
                ->orWhere('staff_number', 'like', $wildcard)
                ->orWhere('nin', 'like', $wildcard)
                ->orWhere('passport_number', 'like', $wildcard)
                ->orWhere('phone', 'like', $wildcard);
        })->with('business')->latest()->paginate(20, ['*'], 'staff_page')->withQueryString();

        AuditService::logAction(
            action: 'government_search',
            description: $searchTerm,
        );

        return view('government.search', compact('results', 'staffResults', 'request'));
    }

    public function show(Business $business): View
    {
        $this->authorize('view', $business);

        $business->load(['documents.documentType', 'verificationReviews.admin', 'sector']);

        AuditService::logAction(
            action: 'government_view_business',
            description: "Viewed business: {$business->business_name}",
            auditable: $business,
        );

        return view('government.show', compact('business'));
    }

    public function recordCheck(Request $request, Business $business): RedirectResponse
    {
        $this->authorize('view', $business);

        AuditService::logAction(
            action: 'government_verification_check',
            description: "Verification check on: {$business->business_name}",
            auditable: $business,
        );

        return back()->with('success', 'Verification check recorded successfully.');
    }
}
