<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Fee;
use App\Models\StaffMember;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessOwnerDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $businesses = $user->businesses()
            ->with(['sector', 'profile', 'documents'])
            ->latest()
            ->get();
        $businessIds = $businesses->pluck('id');
        $statusCounts = $businesses->countBy('status');

        $counts = [
            'total' => $businesses->count(),
            'draft' => $statusCounts->get('draft', 0),
            'submitted' => $statusCounts->get('submitted', 0) + $statusCounts->get('under_review', 0),
            'correction_required' => $statusCounts->get('correction_required', 0),
            'approved' => $statusCounts->get('approved', 0),
            'verified' => $businesses->filter->is_verified->count(),
            'expired' => $businesses->filter(fn (Business $business): bool => $business->status === 'expired' || (bool) $business->is_expired)->count(),
        ];

        $expiringBusinesses = Business::whereIn('id', $businessIds)
            ->where('status', 'verified')
            ->whereNotNull('verification_expires_at')
            ->whereBetween('verification_expires_at', [now(), now()->addDays(30)])
            ->orderBy('verification_expires_at')
            ->get();

        $staffStatusCounts = StaffMember::whereIn('business_id', $businessIds)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $staffCounts = [
            'total' => (int) $staffStatusCounts->sum(),
            'pending_review' => (int) $staffStatusCounts->get('submitted', 0),
            'approved' => (int) $staffStatusCounts->get('approved', 0),
            'correction_required' => (int) $staffStatusCounts->get('correction_required', 0),
        ];

        $feeStatusCounts = Fee::whereIn('business_id', $businessIds)
            ->selectRaw('payment_status, count(*) as total, sum(amount) as amount')
            ->groupBy('payment_status')
            ->get()
            ->keyBy('payment_status');
        $feeCounts = [
            'unpaid' => (int) ($feeStatusCounts->get('unpaid')?->total ?? 0),
            'pending_confirmation' => (int) ($feeStatusCounts->get('pending_confirmation')?->total ?? 0),
            'unpaid_amount' => (float) ($feeStatusCounts->get('unpaid')?->amount ?? 0),
        ];

        $actionItems = $businesses
            ->map(fn (Business $business): array => $this->nextAction($business))
            ->filter(fn (array $action): bool => $action['priority'] !== 'complete')
            ->sortBy(fn (array $action): int => match ($action['priority']) {
                'urgent' => 1,
                'action' => 2,
                default => 3,
            })
            ->take(6)
            ->values();

        return view('dashboard.business-owner.index', [
            'user' => $user,
            'businesses' => $businesses,
            'counts' => $counts,
            'pendingCorrections' => $counts['correction_required'],
            'expiringBusinesses' => $expiringBusinesses,
            'staffCounts' => $staffCounts,
            'feeCounts' => $feeCounts,
            'actionItems' => $actionItems,
        ]);
    }

    private function nextAction(Business $business): array
    {
        $documentIssueCount = $business->documents->whereIn('status', ['rejected', 'expired'])->count();

        if ($documentIssueCount > 0) {
            return $this->action($business, 'Resolve document issues', "{$documentIssueCount} document(s) need attention.", 'urgent');
        }

        return match ($business->status) {
            'draft' => $this->action($business, 'Complete and submit registration', 'Finish the business record and required documents.', 'action'),
            'correction_required' => $this->action($business, 'Respond to requested corrections', 'An administrator returned this application for changes.', 'urgent'),
            'submitted', 'under_review' => $this->action($business, 'Application under review', 'No action is required unless NB-CCI requests changes.', 'waiting'),
            'approved' => $this->action($business, 'Await manual verification', 'NB-CCI will complete a phone call or site visit.', 'waiting'),
            'verified' => match ($business->profile?->status) {
                null => $this->action($business, 'Create public directory profile', 'Add services, locations, and your business logo.', 'action', route('business-owner.businesses.profile.show', $business)),
                'pending' => $this->action($business, 'Directory profile under review', 'Your public listing is awaiting approval.', 'waiting', route('business-owner.businesses.profile.show', $business)),
                'rejected' => $this->action($business, 'Update rejected directory profile', 'Review the administrator note and resubmit.', 'urgent', route('business-owner.businesses.profile.show', $business)),
                default => $this->action($business, 'Profile published', 'Your verified business is visible in the public directory.', 'complete', route('public.directory.show', $business->profile)),
            },
            'expired' => $this->action($business, 'Request verification renewal', 'Verification has expired and must be renewed.', 'urgent', route('business-owner.renewals.index')),
            'rejected' => $this->action($business, 'Review rejection decision', 'Open the business record for the decision details.', 'urgent'),
            'suspended' => $this->action($business, 'Contact NB-CCI support', 'This business account is currently suspended.', 'urgent'),
            default => $this->action($business, 'Review business status', 'Open the record for current details.', 'waiting'),
        };
    }

    private function action(Business $business, string $label, string $description, string $priority, ?string $url = null): array
    {
        return [
            'business' => $business,
            'label' => $label,
            'description' => $description,
            'priority' => $priority,
            'url' => $url ?? route('business-owner.businesses.show', $business),
        ];
    }
}
