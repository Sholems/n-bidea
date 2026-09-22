<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffDocument;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminStaffDocumentController extends Controller
{
    public function review(Request $request, StaffDocument $staffDocument): RedirectResponse
    {
        $this->authorize('review', $staffDocument->staffMember);

        $validated = $request->validate([
            'status' => ['required', 'in:accepted,rejected'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($staffDocument, $validated): void {
            $locked = StaffDocument::whereKey($staffDocument->getKey())->lockForUpdate()->firstOrFail();

            abort_if(
                $locked->status === $validated['status'],
                422,
                'This document has already been reviewed with this decision.'
            );

            $locked->update([
                'status' => $validated['status'],
                'admin_note' => $validated['note'] ?? null,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            AuditService::logAction(
                action: "staff_document.reviewed.{$validated['status']}",
                description: "Staff document #{$locked->id} review decision: {$validated['status']}",
                auditable: $locked,
            );
        });

        return back()->with('success', 'Document review submitted successfully.');
    }

    public function download(StaffDocument $staffDocument): StreamedResponse
    {
        $this->authorize('view', $staffDocument->staffMember);

        abort_unless(Storage::disk('private')->exists($staffDocument->file_path), 404);

        return Storage::disk('private')->download(
            $staffDocument->file_path,
            $staffDocument->original_file_name,
        );
    }
}
