<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessDocument;
use App\Models\BusinessProfile;
use App\Models\Fee;
use App\Models\RenewalRequest;
use App\Models\StaffMember;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $statusCounts = Business::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = collect([
            'total' => $statusCounts->sum(),
            'submitted' => $statusCounts->get('submitted', 0),
            'under_review' => $statusCounts->get('under_review', 0),
            'correction_required' => $statusCounts->get('correction_required', 0),
            'approved' => $statusCounts->get('approved', 0),
            'rejected' => $statusCounts->get('rejected', 0),
            'verified' => Business::where('status', 'verified')->where('verification_expires_at', '>', now())->count(),
            'expired' => Business::where('status', 'expired')
                ->orWhere(fn (Builder $query) => $query->where('status', 'verified')->where('verification_expires_at', '<=', now()))
                ->count(),
        ])->map(fn ($count): int => (int) $count)->all();

        $operationalCounts = [
            'fees_pending_confirmation' => Fee::where('payment_status', 'pending_confirmation')->count(),
            'renewals_pending' => RenewalRequest::where('status', 'pending')->count(),
            'staff_pending_review' => StaffMember::where('status', 'submitted')->count(),
            'profiles_pending_review' => BusinessProfile::where('status', 'pending')->count(),
            'documents_pending_review' => BusinessDocument::where('status', 'pending')->count(),
        ];

        $recentSubmissions = Business::whereIn('status', ['submitted', 'under_review'])
            ->with('user', 'sector')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $pendingReviews = Business::whereIn('status', ['submitted', 'under_review'])
            ->with('user', 'sector')
            ->oldest('created_at')
            ->limit(8)
            ->get();

        $actionQueues = collect([
            $this->queueSummary('Applications', Business::whereIn('status', ['submitted', 'under_review']), route('admin.businesses.index', ['queue' => 'pending_review']), 'bi-clipboard-check'),
            $this->queueSummary('Business documents', BusinessDocument::where('status', 'pending'), route('admin.businesses.index'), 'bi-file-earmark-check'),
            $this->queueSummary('Directory profiles', BusinessProfile::where('status', 'pending'), route('admin.business-profiles.index', ['status' => 'pending']), 'bi-shop'),
            $this->queueSummary('Staff clearance', StaffMember::where('status', 'submitted'), route('admin.staff.index', ['status' => 'submitted']), 'bi-person-badge'),
            $this->queueSummary('Fee confirmations', Fee::where('payment_status', 'pending_confirmation'), route('admin.fees.index', ['status' => 'pending_confirmation']), 'bi-credit-card'),
            $this->queueSummary('Renewals', RenewalRequest::where('status', 'pending'), route('admin.renewals.index', ['status' => 'pending']), 'bi-arrow-repeat'),
        ]);

        return view('dashboard.admin.index', [
            'counts' => $counts,
            'operationalCounts' => $operationalCounts,
            'recentSubmissions' => $recentSubmissions,
            'pendingReviews' => $pendingReviews,
            'actionQueues' => $actionQueues,
        ]);
    }

    private function queueSummary(string $label, Builder $query, string $url, string $icon): array
    {
        $oldest = (clone $query)->min('created_at');

        return [
            'label' => $label,
            'count' => (clone $query)->count(),
            'oldest_at' => $oldest ? Carbon::parse($oldest) : null,
            'url' => $url,
            'icon' => $icon,
        ];
    }
}
