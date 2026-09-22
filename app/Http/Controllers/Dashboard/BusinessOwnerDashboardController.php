<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessOwnerDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $businesses = $user->businesses;

        $counts = [
            'total' => $businesses->count(),
            'draft' => $businesses->where('status', 'draft')->count(),
            'submitted' => $businesses->where('status', 'submitted')->count(),
            'verified' => $businesses->whereIn('status', ['approved', 'verified'])->count(),
            'expired' => $businesses->where('status', 'expired')->count(),
        ];

        $pendingCorrections = $businesses->where('status', 'correction_required')->count();

        $latestBusiness = $businesses->sortByDesc('created_at')->first();

        return view('dashboard.business-owner.index', [
            'user' => $user,
            'businesses' => $businesses,
            'counts' => $counts,
            'pendingCorrections' => $pendingCorrections,
            'latestBusiness' => $latestBusiness,
        ]);
    }
}
