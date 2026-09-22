<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Requests\AgencyRequest;
use App\Models\Agency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SuperAdminAgencyController extends SuperAdminResourceController
{
    protected function modelClass(): string
    {
        return Agency::class;
    }

    protected function label(): string
    {
        return 'Agency';
    }

    protected function routeBaseName(): string
    {
        return 'super-admin.agencies';
    }

    protected function viewBaseName(): string
    {
        return 'super-admin.agencies';
    }

    protected function auditKey(): string
    {
        return 'agency';
    }

    protected function indexQuery(): Builder
    {
        return Agency::query()->withCount('users')->latest();
    }

    protected function deletionBlockedMessage(Model $model): ?string
    {
        return $model->users()->exists()
            ? 'This agency cannot be deleted while users are assigned to it.'
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

    public function store(AgencyRequest $request): RedirectResponse
    {
        return $this->persistStore($request);
    }

    public function edit(Agency $agency): View
    {
        return $this->renderEdit($agency);
    }

    public function update(AgencyRequest $request, Agency $agency): RedirectResponse
    {
        return $this->persistUpdate($request, $agency);
    }

    public function destroy(Agency $agency): RedirectResponse
    {
        return $this->removeResource($agency);
    }
}
