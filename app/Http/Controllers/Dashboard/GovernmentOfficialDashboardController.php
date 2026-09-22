<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GovernmentOfficialDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $recentChecks = AuditLog::where('user_id', $request->user()->id)
            ->where(function ($query) {
                $query->where('action', 'like', '%verif%')
                    ->orWhere('action', 'like', '%check%');
            })
            ->with('auditable')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('dashboard.government.index', [
            'recentChecks' => $recentChecks,
        ]);
    }
}
