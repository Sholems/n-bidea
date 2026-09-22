<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Business;
use App\Models\RenewalRequest;
use App\Models\User;
use Illuminate\View\View;

class SuperAdminDashboardController extends Controller
{
    public function index(): View
    {
        $counts = [
            'total_users' => User::count(),
            'total_businesses' => Business::count(),
            'total_verified' => Business::whereIn('status', ['approved', 'verified'])->count(),
            'total_officials' => User::where('role', 'government_official')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'pending_applications' => Business::where('status', 'submitted')->count(),
            'expired' => Business::where('status', 'expired')->count(),
            'renewal_requests' => RenewalRequest::where('status', 'pending')->count(),
        ];

        $recentActivity = AuditLog::with('user')
            ->orderByDesc('created_at')
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
            'recentActivity' => $recentActivity,
            'reports' => $reports,
        ]);
    }
}
