<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\Certificate;
use App\Models\StaffMember;
use App\Services\AuditService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

#[Signature('verifications:expire')]
#[Description('Mark businesses and certificates as expired once their verification has lapsed.')]
class ExpireVerifications extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();

        $stats = [
            'businesses' => 0,
            'certificates' => 0,
            'staff' => 0,
        ];

        $businessIds = Business::query()
            ->where('status', 'verified')
            ->whereNotNull('verification_expires_at')
            ->where('verification_expires_at', '<=', $now)
            ->pluck('id');

        $this->withProgressBar($businessIds->chunk(100), function (Collection $chunk) use (&$stats, $now): void {
            foreach ($chunk as $businessId) {
                DB::transaction(function () use ($businessId, &$stats, $now): void {
                    $business = Business::whereKey($businessId)->lockForUpdate()->first();

                    if (! $business) {
                        return;
                    }

                    if ($business->status !== 'verified') {
                        return;
                    }

                    if (! $business->verification_expires_at || $business->verification_expires_at->gt($now)) {
                        return;
                    }

                    $business->update(['status' => 'expired']);

                    $certificates = Certificate::query()
                        ->where('business_id', $business->id)
                        ->where('status', 'active')
                        ->whereNotNull('expires_at')
                        ->where('expires_at', '<=', $now)
                        ->get();

                    foreach ($certificates as $certificate) {
                        $certificate->update(['status' => 'expired']);

                        AuditService::logAction(
                            action: 'certificate.expired',
                            description: "Certificate '{$certificate->certificate_number}' for business '{$business->business_name}' has expired",
                            auditable: $certificate,
                        );

                        $stats['certificates']++;
                    }

                    AuditService::logAction(
                        action: 'business.verification_expired',
                        description: "Verification for business '{$business->business_name}' has expired",
                        auditable: $business,
                    );

                    $stats['businesses']++;
                }, 5);
            }
        });

        StaffMember::query()
            ->where('status', 'approved')
            ->whereNotNull('verification_expires_at')
            ->where('verification_expires_at', '<=', $now)
            ->lazyById()
            ->each(function (StaffMember $staffMember) use (&$stats): void {
                $staffMember->update(['status' => 'expired']);

                AuditService::logAction(
                    action: 'staff.verification_expired',
                    description: "Clearance for staff member '{$staffMember->full_name}' has expired",
                    auditable: $staffMember,
                );

                $stats['staff']++;
            });

        $this->newLine();
        $this->info("Expired {$stats['businesses']} business(es), {$stats['certificates']} certificate(s) and {$stats['staff']} staff member(s).");

        return self::SUCCESS;
    }
}
