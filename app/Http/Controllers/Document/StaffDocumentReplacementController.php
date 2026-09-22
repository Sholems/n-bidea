<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentReplacementRequest;
use App\Models\StaffDocument;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class StaffDocumentReplacementController extends Controller
{
    public function replace(DocumentReplacementRequest $request, StaffDocument $staffDocument): RedirectResponse
    {
        $staffMember = $staffDocument->staffMember;

        $this->authorize('manageDocuments', $staffMember);

        abort_if($staffDocument->status === 'accepted', 422, 'Accepted documents cannot be replaced.');

        $previousPath = $staffDocument->file_path;

        $file = $request->file('file');
        $path = $file->store("staff-documents/{$staffMember->id}", 'private');

        $staffDocument->update([
            'file_path' => $path,
            'original_file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'status' => 'pending',
            'admin_note' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

        if ($previousPath !== $path) {
            Storage::disk('private')->delete($previousPath);
        }

        AuditService::logAction(
            action: 'staff_document.replaced',
            description: "Document replaced for staff member '{$staffMember->full_name}': {$file->getClientOriginalName()}",
            auditable: $staffDocument,
        );

        return back()->with('success', 'Document replaced successfully.');
    }
}
