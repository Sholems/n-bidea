<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Requests\DocumentTypeRequest;
use App\Models\StaffDocumentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SuperAdminStaffDocumentTypeController extends SuperAdminResourceController
{
    protected function modelClass(): string
    {
        return StaffDocumentType::class;
    }

    protected function label(): string
    {
        return 'Staff document type';
    }

    protected function routeBaseName(): string
    {
        return 'super-admin.staff-document-types';
    }

    protected function viewBaseName(): string
    {
        return 'super-admin.staff-document-types';
    }

    protected function auditKey(): string
    {
        return 'staff_document_type';
    }

    protected function indexQuery(): Builder
    {
        return StaffDocumentType::query()->withCount('documents')->latest();
    }

    protected function deletionBlockedMessage(Model $model): ?string
    {
        return $model->documents()->exists()
            ? 'This staff document type cannot be deleted while documents use it.'
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

    public function edit(StaffDocumentType $staffDocumentType): View
    {
        return $this->renderEdit($staffDocumentType);
    }

    public function update(DocumentTypeRequest $request, StaffDocumentType $staffDocumentType): RedirectResponse
    {
        return $this->persistUpdate($request, $staffDocumentType);
    }

    public function destroy(StaffDocumentType $staffDocumentType): RedirectResponse
    {
        return $this->removeResource($staffDocumentType);
    }
}
