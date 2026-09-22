<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Requests\SectorRequest;
use App\Models\Sector;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SuperAdminSectorController extends SuperAdminResourceController
{
    protected function modelClass(): string
    {
        return Sector::class;
    }

    protected function label(): string
    {
        return 'Sector';
    }

    protected function routeBaseName(): string
    {
        return 'super-admin.sectors';
    }

    protected function viewBaseName(): string
    {
        return 'super-admin.sectors';
    }

    protected function auditKey(): string
    {
        return 'sector';
    }

    protected function indexQuery(): Builder
    {
        return Sector::query()->withCount('businesses')->latest();
    }

    protected function deletionBlockedMessage(Model $model): ?string
    {
        return $model->businesses()->exists()
            ? 'This sector cannot be deleted while businesses are assigned to it.'
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

    public function store(SectorRequest $request): RedirectResponse
    {
        return $this->persistStore($request);
    }

    public function edit(Sector $sector): View
    {
        return $this->renderEdit($sector);
    }

    public function update(SectorRequest $request, Sector $sector): RedirectResponse
    {
        return $this->persistUpdate($request, $sector);
    }

    public function destroy(Sector $sector): RedirectResponse
    {
        return $this->removeResource($sector);
    }
}
