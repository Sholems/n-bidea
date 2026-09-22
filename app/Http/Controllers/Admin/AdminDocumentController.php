<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessDocument;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDocumentController extends Controller
{
    public function index(Business $business): View
    {
        $this->authorize('viewAny', BusinessDocument::class);

        $documents = $business->documents()
            ->with('documentType')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.documents.index', compact('business', 'documents'));
    }

    public function show(BusinessDocument $document): View
    {
        $this->authorize('view', $document);

        $document->load(['business', 'documentType', 'reviewer']);

        return view('admin.documents.show', compact('document'));
    }

    public function review(Request $request, BusinessDocument $document): RedirectResponse
    {
        $this->authorize('review', $document);

        $validated = $request->validate([
            'status' => ['required', 'in:accepted,rejected'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($document, $validated): void {
            $locked = BusinessDocument::whereKey($document->getKey())->lockForUpdate()->firstOrFail();

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
                action: "document.reviewed.{$validated['status']}",
                description: "Document #{$locked->id} review decision: {$validated['status']}",
                auditable: $locked,
            );
        });

        return back()->with('success', 'Document review submitted successfully.');
    }

    public function download(BusinessDocument $document): StreamedResponse
    {
        $this->authorize('view', $document);

        abort_unless(Storage::disk('private')->exists($document->file_path), 404);

        return Storage::disk('private')->download(
            $document->file_path,
            $document->original_file_name,
        );
    }
}
