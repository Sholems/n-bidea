<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Fee;
use App\Models\Setting;

class FeeService
{
    /**
     * Raise an unpaid fee of the given type for a business.
     *
     * The amount comes from the `{type}_fee` setting. When no positive amount
     * is configured, no fee is raised and null is returned.
     *
     * @param  'certification'|'renewal'  $feeType
     */
    public static function issue(Business $business, string $feeType): ?Fee
    {
        $amount = (float) Setting::get("{$feeType}_fee", 0);

        if ($amount <= 0) {
            return null;
        }

        $fee = $business->fees()->create([
            'fee_type' => $feeType,
            'amount' => $amount,
            'payment_status' => 'unpaid',
        ]);

        AuditService::logAction(
            action: 'fee.issued',
            description: "{$feeType} fee of ".number_format($amount, 2)." issued for business '{$business->business_name}'",
            auditable: $fee,
        );

        return $fee;
    }
}
