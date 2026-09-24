<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RenewalRequest;
use App\Notifications\RenewalRequestDecisionNotification;
use App\Services\AuditService;
use App\Services\CertificateNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminRenewalController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('is-admin-or-super');

        $query = RenewalRequest::with('business', 'requester');

        if ($status = $request->string('status')->trim()->toString()) {
            $query->where('status', $status);
        }

        $renewalRequests = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.renewal.index', compact('renewalRequests'));
    }

    public function show(RenewalRequest $renewalRequest): View
    {
        $this->authorize('is-admin-or-super');

        $renewalRequest->load('business');

        return view('admin.renewal.show', compact('renewalRequest'));
    }

    public function approve(RenewalRequest $renewalRequest): RedirectResponse
    {
        $this->authorize('is-admin-or-super');

        $approved = $this->retryOnUniqueViolation(function () use ($renewalRequest): RenewalRequest {
            return DB::transaction(function () use ($renewalRequest): RenewalRequest {
                $locked = RenewalRequest::whereKey($renewalRequest->getKey())->lockForUpdate()->firstOrFail();

                abort_if($locked->status !== 'pending', 422, 'This renewal request has already been processed.');

                $business = $locked->business()->lockForUpdate()->firstOrFail();

                $baseDate = $business->verification_expires_at ?? now();
                $newExpiryDate = $baseDate->copy()->addYear();

                $locked->update([
                    'status' => 'approved',
                    'admin_id' => auth()->id(),
                    'new_expiry_date' => $newExpiryDate,
                ]);

                $business->update([
                    'verification_expires_at' => $newExpiryDate,
                    'verified_at' => now(),
                    'status' => 'verified',
                ]);

                $certificate = CertificateNumberService::issueForBusiness($business->refresh(), auth()->user(), $newExpiryDate);

                AuditService::logAction(
                    action: 'certificate.renewed',
                    description: "Certificate '{$certificate->certificate_number}' renewed for business '{$business->business_name}'",
                    auditable: $certificate,
                );

                AuditService::logAction(
                    action: 'renewal.approved',
                    description: "Renewal request #{$locked->id} approved for business '{$business->business_name}'",
                    auditable: $locked,
                );

                return $locked->refresh();
            }, 5);
        });

        $approved->requester->notify(new RenewalRequestDecisionNotification($approved, 'approved'));

        return back()->with('success', 'Renewal request approved successfully.');
    }

    public function reject(Request $request, RenewalRequest $renewalRequest): RedirectResponse
    {
        $this->authorize('is-admin-or-super');

        $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $adminNote = $request->input('admin_note');

        DB::transaction(function () use ($renewalRequest, $adminNote): void {
            $locked = RenewalRequest::whereKey($renewalRequest->getKey())->lockForUpdate()->firstOrFail();

            abort_if($locked->status !== 'pending', 422, 'This renewal request has already been processed.');

            $locked->update([
                'status' => 'rejected',
                'admin_id' => auth()->id(),
                'admin_note' => $adminNote,
            ]);

            AuditService::logAction(
                action: 'renewal.rejected',
                description: "Renewal request #{$locked->id} rejected for business '{$locked->business->business_name}'",
                auditable: $locked,
            );
        });

        $renewalRequest->refresh()->requester->notify(new RenewalRequestDecisionNotification($renewalRequest->refresh(), 'rejected'));

        return back()->with('success', 'Renewal request rejected.');
    }
}
