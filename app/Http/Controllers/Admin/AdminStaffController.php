<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerificationReviewRequest;
use App\Models\StaffMember;
use App\Notifications\StaffReviewDecisionNotification;
use App\Services\AuditService;
use App\Services\StaffNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminStaffController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', StaffMember::class);

        $query = StaffMember::with('business');

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('staff_number', 'like', "%{$search}%")
                    ->orWhereHas('business', fn ($business) => $business->where('business_name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->string('status')->trim()->toString()) {
            $query->where('status', $status);
        }

        $staffMembers = $query->latest()->paginate(20)->withQueryString();

        return view('admin.staff.index', compact('staffMembers'));
    }

    public function show(StaffMember $staffMember): View
    {
        $this->authorize('view', $staffMember);

        $staffMember->load(['business.user', 'documents.documentType', 'reviewer']);

        return view('admin.staff.show', compact('staffMember'));
    }

    public function review(VerificationReviewRequest $request, StaffMember $staffMember): RedirectResponse
    {
        $this->authorize('review', $staffMember);

        $decision = $request->input('decision');
        $note = $request->input('note');

        $reviewed = $this->retryOnUniqueViolation(function () use ($staffMember, $decision, $note): StaffMember {
            return DB::transaction(function () use ($staffMember, $decision, $note): StaffMember {
                $locked = StaffMember::with('business')->whereKey($staffMember->getKey())->lockForUpdate()->firstOrFail();

                abort_unless($locked->status === 'submitted', 422, 'Only submitted staff records can be reviewed.');
                abort_unless(
                    in_array($locked->business->status, ['approved', 'verified'], true),
                    422,
                    'The business must be approved before its staff can be reviewed.'
                );

                $attributes = [
                    'status' => $decision,
                    'review_note' => $note,
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ];

                if ($decision === 'approved') {
                    $attributes += [
                        'staff_number' => $locked->staff_number ?? StaffNumberService::generate(),
                        'verified_at' => now(),
                        'verification_expires_at' => now()->addYear(),
                    ];
                }

                $locked->update($attributes);

                AuditService::logAction(
                    action: "staff.reviewed.{$decision}",
                    description: "Staff member '{$locked->full_name}' review decision: {$decision}",
                    auditable: $locked,
                );

                return $locked;
            }, 5);
        });

        $reviewed->business->user->notify(new StaffReviewDecisionNotification($reviewed, $decision, $note));

        return back()->with('success', 'Staff review submitted successfully.');
    }
}
