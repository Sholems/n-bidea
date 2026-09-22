<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $counts = [
            'total' => Business::count(),
            'submitted' => Business::where('status', 'submitted')->count(),
            'under_review' => Business::where('status', 'under_review')->count(),
            'correction_required' => Business::where('status', 'correction_required')->count(),
            'approved' => Business::where('status', 'approved')->count(),
            'rejected' => Business::where('status', 'rejected')->count(),
            'expired' => Business::where('status', 'expired')->count(),
            'verified' => Business::where('status', 'verified')->count(),
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
            'recentSubmissions' => $recentSubmissions,
            'pendingReviews' => $pendingReviews,
        ]);
    }
}
