<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\RenewalRequestStoreRequest;
use App\Models\RenewalRequest;
use App\Services\AuditService;
use App\Services\FeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BusinessRenewalController extends Controller
{
    public function index(Request $request): View
    {
        $renewalRequests = RenewalRequest::where('requested_by', $request->user()->id)
            ->with('business')
            ->latest()
            ->paginate(15);

        $businesses = $request->user()->businesses()
            ->withCount([
                'renewalRequests as pending_renewal_count' => fn ($query) => $query->where('status', 'pending'),
            ])
            ->orderBy('business_name')
            ->get();

        return view('business.renewal.index', compact('renewalRequests', 'businesses'));
    }

    public function store(RenewalRequestStoreRequest $request): RedirectResponse
    {
        $user = $request->user();
        $businessId = $request->input('business_id');
        $reason = $request->input('reason');

        $renewal = DB::transaction(function () use ($user, $businessId, $reason): ?RenewalRequest {
            $business = $user->businesses()->whereKey($businessId)->lockForUpdate()->firstOrFail();

            if (! in_array($business->status, ['approved', 'verified', 'expired'], true)) {
                throw ValidationException::withMessages([
                    'business_id' => 'Only approved or expired businesses can be renewed.',
                ]);
            }

            $hasPending = RenewalRequest::where('business_id', $business->id)
                ->where('requested_by', $user->id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->exists();

            if ($hasPending) {
                return null;
            }

            $renewalRequest = RenewalRequest::create([
                'business_id' => $business->id,
                'requested_by' => $user->id,
                'status' => 'pending',
                'reason' => $reason,
                'previous_expiry_date' => $business->verification_expires_at,
            ]);

            FeeService::issue($business, 'renewal');

            return $renewalRequest;
        });

        if ($renewal === null) {
            return back()->with('error', 'A pending renewal request already exists for this business.');
        }

        AuditService::logAction(
            action: 'renewal.requested',
            description: "Renewal requested for business '{$renewal->business->business_name}'",
            auditable: $renewal,
        );

        return back()->with('success', 'Renewal request submitted successfully.');
    }
}
