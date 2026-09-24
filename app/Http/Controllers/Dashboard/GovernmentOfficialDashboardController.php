<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GovernmentOfficialDashboardController extends Controller
{
    /**
     * The audit actions this official's own checks are logged under. Kept
     * as an explicit list rather than a text search, so a lookup only shows
     * up here if it is actually one of these actions.
     *
     * @var array<int, string>
     */
    private const LOOKUP_ACTIONS = [
        'government_view_business',
        'government_view_staff',
    ];

    private const ACTIVITY_ACTIONS = [
        'government_verification_check',
        ...self::LOOKUP_ACTIONS,
    ];

    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $checks = fn () => AuditLog::where('user_id', $userId)
            ->where('action', 'government_verification_check');
        $lookups = fn () => AuditLog::where('user_id', $userId)
            ->whereIn('action', self::LOOKUP_ACTIONS);

        $counts = [
            'checks_today' => $checks()->whereDate('created_at', today())->count(),
            'checks_this_week' => $checks()->where('created_at', '>=', now()->startOfWeek())->count(),
            'checks_total' => $checks()->count(),
            'lookups_today' => $lookups()->whereDate('created_at', today())->count(),
            'lookups_total' => $lookups()->count(),
        ];

        $recentActivity = AuditLog::where('user_id', $userId)
            ->whereIn('action', self::ACTIVITY_ACTIONS)
            ->with('auditable')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('dashboard.government.index', [
            'counts' => $counts,
            'recentActivity' => $recentActivity,
        ]);
    }
}
