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
    private const CHECK_ACTIONS = [
        'government_verification_check',
        'government_view_business',
        'government_view_staff',
    ];

    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $baseQuery = fn () => AuditLog::where('user_id', $userId)
            ->whereIn('action', self::CHECK_ACTIONS);

        $counts = [
            'today' => $baseQuery()->whereDate('created_at', today())->count(),
            'this_week' => $baseQuery()->where('created_at', '>=', now()->startOfWeek())->count(),
            'total' => $baseQuery()->count(),
        ];

        $recentChecks = $baseQuery()
            ->with('auditable')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('dashboard.government.index', [
            'counts' => $counts,
            'recentChecks' => $recentChecks,
        ]);
    }
}
