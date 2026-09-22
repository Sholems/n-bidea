<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessVerificationDecisionRequest;
use App\Models\Business;
use App\Models\BusinessVerificationCheck;
use App\Models\Setting;
use App\Notifications\BusinessVerificationDecisionNotification;
use App\Services\AuditService;
use App\Services\CertificateNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class BusinessVerificationController extends Controller
{
    public function store(BusinessVerificationDecisionRequest $request, Business $business): RedirectResponse
    {
        $validated = $request->validated();
        $check = DB::transaction(function () use ($business, $validated, $request): BusinessVerificationCheck {
            $locked = Business::whereKey($business->getKey())->lockForUpdate()->firstOrFail();

            abort_unless(
                $locked->status === 'approved',
                422,
                'Only an approved business awaiting verification can be checked.'
            );

            $isVerified = $validated['decision'] === 'verified';
            $expiresAt = $isVerified
                ? now()->addMonths(max(1, (int) Setting::get('verification_validity_months', 12)))
                : null;

            $locked->update([
                'status' => $isVerified ? 'verified' : 'approved',
                'verified_at' => $isVerified ? now() : null,
                'verification_expires_at' => $expiresAt,
            ]);

            $check = BusinessVerificationCheck::create([
                'business_id' => $locked->id,
                'super_admin_id' => $request->user()->id,
                'method' => $validated['method'],
                'decision' => $validated['decision'],
                'note' => $validated['note'],
                'checked_at' => now(),
                'expires_at' => $expiresAt,
            ]);

            if ($isVerified) {
                $certificate = CertificateNumberService::issueForBusiness($locked->refresh(), $request->user(), $expiresAt);

                AuditService::logAction(
                    action: 'certificate.issued',
                    description: "Certificate '{$certificate->certificate_number}' issued after {$check->method} verification for '{$locked->business_name}'",
                    auditable: $certificate,
                );
            }

            AuditService::logAction(
                action: "business.verification.{$validated['decision']}",
                description: "Business '{$locked->business_name}' verification decision recorded after {$check->method}",
                auditable: $check,
            );

            return $check;
        }, 5);

        $business->refresh()->user->notify(
            new BusinessVerificationDecisionNotification($business, $check)
        );

        return redirect()->route('admin.businesses.show', $business)
            ->with('success', 'Business verification decision recorded successfully.');
    }
}
