<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentUploadRequest;
use App\Models\Business;
use App\Models\BusinessDocument;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Business $business): View
    {
        $this->authorize('view', $business);

        $business->load('documents.documentType');

        return view('documents.index', compact('business'));
    }

    public function store(DocumentUploadRequest $request, Business $business): RedirectResponse
    {
        $this->authorize('create', $business);

        $file = $request->file('file');
        $path = $file->store("business-documents/{$business->id}", 'private');

        try {
            $document = BusinessDocument::create([
                'business_id' => $business->id,
                'document_type_id' => $request->input('document_type_id'),
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
            action: 'document.uploaded',
            description: "Document uploaded: {$file->getClientOriginalName()}",
            auditable: $document,
        );

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function destroy(BusinessDocument $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        $business = $document->business;
        $filePath = $document->file_path;
        $fileName = $document->original_file_name;

        $document->delete();

        if ($filePath) {
            Storage::disk('private')->delete($filePath);
        }

        AuditService::logAction(
            action: 'document.deleted',
            description: "Document deleted: {$fileName}",
            auditable: $business,
        );

        return back()->with('success', 'Document deleted successfully.');
    }

    public function download(BusinessDocument $document): StreamedResponse
    {
        $this->authorize('view', $document);

        abort_unless(
            Storage::disk('private')->exists($document->file_path),
            404
        );

        return Storage::disk('private')->download(
            $document->file_path,
            $document->original_file_name,
        );
    }
}
