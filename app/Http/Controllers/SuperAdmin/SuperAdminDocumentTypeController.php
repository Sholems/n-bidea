<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Requests\DocumentTypeRequest;
use App\Models\DocumentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SuperAdminDocumentTypeController extends SuperAdminResourceController
{
    protected function modelClass(): string
    {
        return DocumentType::class;
    }

    protected function label(): string
    {
        return 'Document type';
    }

    protected function routeBaseName(): string
    {
        return 'super-admin.document-types';
    }

    protected function viewBaseName(): string
    {
        return 'super-admin.document-types';
    }

    protected function auditKey(): string
    {
        return 'document_type';
    }

    protected function indexQuery(): Builder
    {
        return DocumentType::query()->withCount('documents')->latest();
    }

    protected function deletionBlockedMessage(Model $model): ?string
    {
        return $model->documents()->exists()
            ? 'This document type cannot be deleted while documents use it.'
            : null;
    }

    public function index(): View
    {
        return $this->renderIndex();
    }

    public function create(): View
    {
        return $this->renderCreate();
    }

    public function store(DocumentTypeRequest $request): RedirectResponse
    {
        return $this->persistStore($request);
    }

    public function edit(DocumentType $documentType): View
    {
        return $this->renderEdit($documentType);
    }

    public function update(DocumentTypeRequest $request, DocumentType $documentType): RedirectResponse
    {
        return $this->persistUpdate($request, $documentType);
    }

    public function destroy(DocumentType $documentType): RedirectResponse
    {
        return $this->removeResource($documentType);
    }
}
