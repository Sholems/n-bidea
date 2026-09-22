<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessProfile;
use App\Models\Fee;
use App\Models\RenewalRequest;
use App\Models\StaffMember;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $counts = [
            'total' => Business::count(),
            'submitted' => Business::where('status', 'submitted')->count(),
            'correction_required' => Business::where('status', 'correction_required')->count(),
            'approved' => Business::where('status', 'approved')->count(),
            'rejected' => Business::where('status', 'rejected')->count(),
            'expired' => Business::where('status', 'expired')->count(),
            'verified' => Business::where('status', 'verified')->count(),
        ];

        $operationalCounts = [
            'fees_pending_confirmation' => Fee::where('payment_status', 'pending_confirmation')->count(),
            'renewals_pending' => RenewalRequest::where('status', 'pending')->count(),
            'staff_pending_review' => StaffMember::where('status', 'submitted')->count(),
            'profiles_pending_review' => BusinessProfile::where('status', 'pending')->count(),
        ];

        $recentSubmissions = Business::with('user', 'sector')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $pendingReviews = Business::whereIn('status', ['submitted', 'under_review'])
            ->with('user', 'sector')
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'pending_page')
            ->withQueryString();

        return view('dashboard.admin.index', [
            'counts' => $counts,
            'operationalCounts' => $operationalCounts,
            'recentSubmissions' => $recentSubmissions,
            'pendingReviews' => $pendingReviews,
        ]);
    }
}
