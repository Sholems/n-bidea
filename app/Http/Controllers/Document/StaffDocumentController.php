<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffDocumentUploadRequest;
use App\Models\StaffDocument;
use App\Models\StaffMember;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffDocumentController extends Controller
{
    public function store(StaffDocumentUploadRequest $request, StaffMember $staffMember): RedirectResponse
    {
        $this->authorize('manageDocuments', $staffMember);

        $file = $request->file('file');
        $path = $file->store("staff-documents/{$staffMember->id}", 'private');

        try {
            $document = $staffMember->documents()->create([
                'staff_document_type_id' => $request->input('staff_document_type_id'),
                'file_path' => $path,
                'original_file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'status' => 'pending',
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('private')->delete($path);

            throw $exception;
        }

        AuditService::logAction(
            action: 'staff_document.uploaded',
            description: "Document uploaded for staff member '{$staffMember->full_name}': {$file->getClientOriginalName()}",
            auditable: $document,
        );

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function destroy(StaffDocument $staffDocument): RedirectResponse
    {
        $staffMember = $staffDocument->staffMember;

        $this->authorize('manageDocuments', $staffMember);

        abort_if($staffDocument->status === 'accepted', 422, 'Accepted documents cannot be removed.');

        $filePath = $staffDocument->file_path;
        $fileName = $staffDocument->original_file_name;

        $staffDocument->delete();

        Storage::disk('private')->delete($filePath);

        AuditService::logAction(
            action: 'staff_document.deleted',
            description: "Document deleted for staff member '{$staffMember->full_name}': {$fileName}",
            auditable: $staffMember,
        );

        return back()->with('success', 'Document deleted successfully.');
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
