<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerificationReviewRequest;
use App\Models\Business;
use App\Models\VerificationReview;
use App\Notifications\BusinessReviewDecisionNotification;
use App\Services\AuditService;
use App\Services\BusinessDirectoryService;
use App\Services\RegistryNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminBusinessController extends Controller
{
    public function __construct(private BusinessDirectoryService $businessDirectoryService) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Business::class);

        $query = Business::with('sector');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                    ->orWhere('registry_number', 'like', "%{$search}%")
                    ->orWhere('business_type', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $businesses = $query->latest()->paginate(20)->withQueryString();

        return view('admin.business.index', compact('businesses'));
    }

    public function show(Business $business): View
    {
        $this->authorize('view', $business);

        $business->load(['documents.documentType', 'verificationReviews.admin', 'verificationChecks.superAdmin', 'fees', 'user', 'sector']);

        $documents = $business->documents;
        $verificationReviews = $business->verificationReviews;
        $fees = $business->fees;

        return view('admin.business.show', compact('business', 'documents', 'verificationReviews', 'fees'));
    }

    public function review(VerificationReviewRequest $request, Business $business): RedirectResponse
    {
        $this->authorize('review', $business);

        $decision = $request->input('decision');
        $note = $request->input('note');

        $reviewed = $this->retryOnUniqueViolation(function () use ($business, $decision, $note): Business {
            return DB::transaction(function () use ($business, $decision, $note): Business {
                $locked = Business::whereKey($business->getKey())->lockForUpdate()->firstOrFail();

                abort_unless(
                    in_array($locked->status, ['draft', 'submitted', 'under_review', 'correction_required'], true),
                    422,
                    'This business has already been reviewed and cannot be reviewed again.'
                );

                $previousStatus = $locked->status;
                $newStatus = match ($decision) {
                    'approved' => 'approved',
                    'rejected' => 'rejected',
                    default => 'correction_required',
                };
                if ($decision === 'approved') {
                    $locked->update([
                        'status' => 'approved',
                        'registry_number' => $locked->registry_number ?? RegistryNumberService::generate(),
                        'verified_at' => null,
                        'verification_expires_at' => null,
                    ]);

                    $this->businessDirectoryService->publishDefaultListing($locked, auth()->user());
                } else {
                    $locked->update(['status' => $newStatus]);
                }

                VerificationReview::create([
                    'business_id' => $locked->id,
                    'admin_id' => auth()->id(),
                    'decision' => $decision,
                    'note' => $note,
                    'previous_status' => $previousStatus,
                    'new_status' => $newStatus,
                    'expires_at' => null,
                ]);

                AuditService::logAction(
                    action: "business.reviewed.{$decision}",
                    description: "Business '{$locked->business_name}' review decision: {$decision}",
                    auditable: $locked,
                );

                return $locked->refresh();
            }, 5);
        });

        $reviewed->user->notify(new BusinessReviewDecisionNotification($reviewed, $reviewed->status, $note));

        return back()->with('success', 'Business review submitted successfully.');
    }
}
