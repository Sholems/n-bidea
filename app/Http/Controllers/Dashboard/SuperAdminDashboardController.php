<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Business;
use App\Models\BusinessDocument;
use App\Models\BusinessProfile;
use App\Models\BusinessVerificationCheck;
use App\Models\DocumentType;
use App\Models\Fee;
use App\Models\RenewalRequest;
use App\Models\Sector;
use App\Models\Setting;
use App\Models\StaffDocumentType;
use App\Models\StaffMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SuperAdminDashboardController extends Controller
{
    public function index(): View
    {
        $statusCounts = Business::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [
            'total_users' => User::count(),
            'total_businesses' => (int) $statusCounts->sum(),
            'total_verified' => Business::where('status', 'verified')->where('verification_expires_at', '>', now())->count(),
            'total_officials' => User::where('role', 'government_official')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'pending_applications' => (int) $statusCounts->get('submitted', 0) + (int) $statusCounts->get('under_review', 0),
            'expired' => Business::where('status', 'expired')
                ->orWhere(fn (Builder $query) => $query->where('status', 'verified')->where('verification_expires_at', '<=', now()))
                ->count(),
            'renewal_requests' => RenewalRequest::where('status', 'pending')->count(),
            'expiring_soon' => Business::where('status', 'verified')
                ->whereBetween('verification_expires_at', [now(), now()->addDays(30)])
                ->count(),
            'suspended_users' => User::where('account_status', 'suspended')->count(),
        ];

        $reviewQueues = [
            'awaiting_verification' => Business::where('status', 'approved')->count(),
            'profiles_pending_review' => BusinessProfile::where('status', 'pending')->count(),
            'staff_pending_review' => StaffMember::where('status', 'submitted')->count(),
            'fees_pending_confirmation' => Fee::where('payment_status', 'pending_confirmation')->count(),
            'documents_pending_review' => BusinessDocument::where('status', 'pending')->count(),
        ];

        $awaitingVerification = Business::where('status', 'approved')
            ->with('sector')
            ->withMax(['verificationReviews as approved_at' => fn ($query) => $query->where('decision', 'approved')], 'created_at')
            ->orderBy('updated_at')
            ->limit(10)
            ->get();

        $actionQueues = collect([
            $this->queueSummary('Applications', Business::whereIn('status', ['submitted', 'under_review']), route('admin.businesses.index', ['queue' => 'pending_review']), 'bi-clipboard-check'),
            $this->queueSummary('Manual verification', Business::where('status', 'approved'), route('super-admin.dashboard').'#awaiting-verification', 'bi-patch-check'),
            $this->queueSummary('Business documents', BusinessDocument::where('status', 'pending'), route('admin.businesses.index'), 'bi-file-earmark-check'),
            $this->queueSummary('Directory profiles', BusinessProfile::where('status', 'pending'), route('admin.business-profiles.index', ['status' => 'pending']), 'bi-shop'),
            $this->queueSummary('Staff clearance', StaffMember::where('status', 'submitted'), route('admin.staff.index', ['status' => 'submitted']), 'bi-person-badge'),
            $this->queueSummary('Fee confirmations', Fee::where('payment_status', 'pending_confirmation'), route('admin.fees.index', ['status' => 'pending_confirmation']), 'bi-credit-card'),
            $this->queueSummary('Renewals', RenewalRequest::where('status', 'pending'), route('admin.renewals.index', ['status' => 'pending']), 'bi-arrow-repeat'),
        ]);

        $verificationInsights = [
            'expiring_30_days' => $counts['expiring_soon'],
            'expiring_60_days' => Business::where('status', 'verified')->whereBetween('verification_expires_at', [now(), now()->addDays(60)])->count(),
            'expiring_90_days' => Business::where('status', 'verified')->whereBetween('verification_expires_at', [now(), now()->addDays(90)])->count(),
            'not_verified_30_days' => BusinessVerificationCheck::where('decision', 'not_verified')->where('checked_at', '>=', now()->subDays(30))->count(),
        ];

        $setupChecklist = collect([
            ['label' => 'Create an active administrator', 'complete' => User::where('role', 'admin')->where('account_status', 'active')->exists(), 'url' => route('super-admin.users.index')],
            ['label' => 'Configure active business sectors', 'complete' => Sector::where('status', 'active')->exists(), 'url' => route('super-admin.sectors.index')],
            ['label' => 'Configure business document types', 'complete' => DocumentType::where('status', 'active')->exists(), 'url' => route('super-admin.document-types.index')],
            ['label' => 'Configure staff document types', 'complete' => StaffDocumentType::where('status', 'active')->exists(), 'url' => route('super-admin.staff-document-types.index')],
            ['label' => 'Review platform settings', 'complete' => Setting::exists(), 'url' => route('super-admin.settings.index')],
        ]);

        $systemChecks = collect([
            ['label' => 'Production URL', 'complete' => str_starts_with((string) config('app.url'), 'https://'), 'detail' => config('app.url')],
            ['label' => 'Outbound email', 'complete' => config('mail.default') !== 'log' && filled(config('mail.from.address')), 'detail' => ucfirst((string) config('mail.default')).' mailer'],
            ['label' => 'Private cloud storage', 'complete' => config('filesystems.disks.private.driver') === 's3' && filled(config('filesystems.disks.private.bucket')) && filled(config('filesystems.disks.private.endpoint')), 'detail' => 'Cloudflare R2 configuration'],
            ['label' => 'Background queue', 'complete' => config('queue.default') !== 'database' || Schema::hasTable((string) config('queue.connections.database.table')), 'detail' => ucfirst((string) config('queue.default')).' connection'],
        ]);

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
            'actionQueues' => $actionQueues,
            'verificationInsights' => $verificationInsights,
            'setupChecklist' => $setupChecklist,
            'systemChecks' => $systemChecks,
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
