<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Notifications\FeeConfirmedNotification;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminFeeController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('is-admin-or-super');

        $query = Fee::with('business');

        if ($status = $request->string('status')->trim()->toString()) {
            $query->where('payment_status', $status);
        }

        $fees = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.fees.index', compact('fees'));
    }

    public function confirm(Fee $fee): RedirectResponse
    {
        $this->authorize('is-admin-or-super');

        DB::transaction(function () use ($fee): void {
            $locked = Fee::whereKey($fee->getKey())->lockForUpdate()->firstOrFail();

            abort_if($locked->payment_status === 'paid', 422, 'This fee has already been confirmed as paid.');

            $locked->update([
                'payment_status' => 'paid',
                'confirmed_by' => auth()->id(),
                'confirmed_at' => now(),
            ]);

            AuditService::logAction(
                action: 'fee.confirmed',
                description: "Fee #{$locked->id} ({$locked->fee_type}) confirmed as paid",
                auditable: $locked,
            );
        });

        $fee->refresh()->business->user->notify(new FeeConfirmedNotification($fee->refresh()));

        return back()->with('success', 'Fee payment confirmed successfully.');
    }
}
