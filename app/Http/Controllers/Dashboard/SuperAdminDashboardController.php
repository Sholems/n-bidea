<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Business;
use App\Models\BusinessProfile;
use App\Models\BusinessVerificationCheck;
use App\Models\Fee;
use App\Models\RenewalRequest;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\View\View;

class SuperAdminDashboardController extends Controller
{
    public function index(): View
    {
        $counts = [
            'total_users' => User::count(),
            'total_businesses' => Business::count(),
            'total_verified' => Business::where('status', 'verified')->count(),
            'total_officials' => User::where('role', 'government_official')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'pending_applications' => Business::where('status', 'submitted')->count(),
            'expired' => Business::where('status', 'expired')->count(),
            'renewal_requests' => RenewalRequest::where('status', 'pending')->count(),
        ];

        $reviewQueues = [
            'awaiting_verification' => Business::where('status', 'approved')->count(),
            'profiles_pending_review' => BusinessProfile::where('status', 'pending')->count(),
            'staff_pending_review' => StaffMember::where('status', 'submitted')->count(),
            'fees_pending_confirmation' => Fee::where('payment_status', 'pending_confirmation')->count(),
        ];

        $awaitingVerification = Business::where('status', 'approved')
            ->with('sector')
            ->orderBy('updated_at')
            ->limit(10)
            ->get();

        $recentActivity = AuditLog::with('user')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $recentVerificationChecks = BusinessVerificationCheck::with('business', 'superAdmin')
            ->orderByDesc('checked_at')
            ->limit(10)
            ->get();

        $reports = [
            'by_sector' => Business::query()
                ->selectRaw('sector_id, count(*) as total')
                ->with('sector')
                ->groupBy('sector_id')
                ->orderByDesc('total')
                ->get(),
            'by_state' => Business::query()
                ->selectRaw('state, count(*) as total')
                ->groupBy('state')
                ->orderByDesc('total')
                ->get(),
            'by_status' => Business::query()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->orderByDesc('total')
                ->get(),
        ];

        return view('dashboard.super-admin.index', [
            'counts' => $counts,
            'reviewQueues' => $reviewQueues,
            'awaitingVerification' => $awaitingVerification,
            'recentActivity' => $recentActivity,
            'recentVerificationChecks' => $recentVerificationChecks,
            'reports' => $reports,
        ]);
    }
}
