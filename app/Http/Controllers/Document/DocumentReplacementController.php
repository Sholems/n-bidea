<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentReplacementRequest;
use App\Models\BusinessDocument;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class DocumentReplacementController extends Controller
{
    public function replace(DocumentReplacementRequest $request, BusinessDocument $document): RedirectResponse
    {
        $this->authorize('update', $document);

        $business = $document->business;
        $previousPath = $document->file_path;

        $file = $request->file('file');
        $path = $file->store("business-documents/{$business->id}", 'private');

        $document->update([
            'file_path' => $path,
            'original_file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'status' => 'pending',
        ]);

        if ($previousPath && $previousPath !== $path) {
            Storage::disk('private')->delete($previousPath);
        }

        AuditService::logAction(
            action: 'document.replaced',
            description: "Document replaced: {$file->getClientOriginalName()}",
            auditable: $document,
        );

        return back()->with('success', 'Document replaced successfully.');
    }
}
