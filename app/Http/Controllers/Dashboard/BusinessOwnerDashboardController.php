<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Fee;
use App\Models\StaffMember;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class BusinessOwnerDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $businessIds = $user->businesses()->pluck('id');

        $counts = [
            'total' => $businessIds->count(),
            'draft' => $this->countByStatus($businessIds, 'draft'),
            'submitted' => $this->countByStatus($businessIds, 'submitted'),
            'correction_required' => $this->countByStatus($businessIds, 'correction_required'),
            'approved' => $this->countByStatus($businessIds, 'approved'),
            'verified' => $this->countByStatus($businessIds, 'verified'),
            'expired' => $this->countByStatus($businessIds, 'expired'),
        ];

        $businesses = $user->businesses()
            ->with('sector')
            ->latest()
            ->limit(10)
            ->get();

        $expiringBusinesses = Business::whereIn('id', $businessIds)
            ->where('status', 'verified')
            ->whereNotNull('verification_expires_at')
            ->whereBetween('verification_expires_at', [now(), now()->addDays(30)])
            ->orderBy('verification_expires_at')
            ->get();

        $staffCounts = [
            'total' => StaffMember::whereIn('business_id', $businessIds)->count(),
            'pending_review' => StaffMember::whereIn('business_id', $businessIds)->where('status', 'submitted')->count(),
            'approved' => StaffMember::whereIn('business_id', $businessIds)->where('status', 'approved')->count(),
            'correction_required' => StaffMember::whereIn('business_id', $businessIds)->where('status', 'correction_required')->count(),
        ];

        $feeCounts = [
            'unpaid' => Fee::whereIn('business_id', $businessIds)->where('payment_status', 'unpaid')->count(),
            'pending_confirmation' => Fee::whereIn('business_id', $businessIds)->where('payment_status', 'pending_confirmation')->count(),
            'unpaid_amount' => (float) Fee::whereIn('business_id', $businessIds)->where('payment_status', 'unpaid')->sum('amount'),
        ];

        return view('dashboard.business-owner.index', [
            'user' => $user,
            'businesses' => $businesses,
            'counts' => $counts,
            'pendingCorrections' => $counts['correction_required'],
            'expiringBusinesses' => $expiringBusinesses,
            'staffCounts' => $staffCounts,
            'feeCounts' => $feeCounts,
        ]);
    }

    /**
     * @param  Collection<int, int>  $businessIds
     */
    private function countByStatus(Collection $businessIds, string $status): int
    {
        return Business::whereIn('id', $businessIds)->where('status', $status)->count();
    }
}
