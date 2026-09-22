<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

abstract class SuperAdminResourceController extends Controller
{
    /**
     * @return class-string<Model>
     */
    abstract protected function modelClass(): string;

    /**
     * Human readable singular label, e.g. "Sector".
     */
    abstract protected function label(): string;

    /**
     * Base route name, e.g. "super-admin.sectors".
     */
    abstract protected function routeBaseName(): string;

    /**
     * Base view name, e.g. "super-admin.sectors".
     */
    abstract protected function viewBaseName(): string;

    /**
     * Audit action prefix, e.g. "sector".
     */
    abstract protected function auditKey(): string;

    protected function indexQuery(): Builder
    {
        return $this->modelClass()::query()->latest();
    }

    /**
     * Return an error message when the resource cannot be deleted, or null when it can.
     */
    protected function deletionBlockedMessage(Model $model): ?string
    {
        return null;
    }

    protected function renderIndex(): View
    {
        $this->authorize('is-super-admin');

        $items = $this->indexQuery()->paginate(20);

        return view("{$this->viewBaseName()}.index", [$this->indexVariable() => $items]);
    }

    protected function renderCreate(): View
    {
        $this->authorize('is-super-admin');

        return view("{$this->viewBaseName()}.create");
    }

    protected function persistStore(FormRequest $request): RedirectResponse
    {
        $this->authorize('is-super-admin');

        $model = $this->modelClass()::create($request->validated());

        AuditService::logAction(
            action: "{$this->auditKey()}.created",
            description: "{$this->label()} '{$model->name}' created by super admin",
            auditable: $model,
        );

        return redirect()->route("{$this->routeBaseName()}.index")
            ->with('success', "{$this->label()} created successfully.");
    }

    protected function renderEdit(Model $model): View
    {
        $this->authorize('is-super-admin');

        return view("{$this->viewBaseName()}.edit", [$this->editVariable($model) => $model]);
    }

    protected function persistUpdate(FormRequest $request, Model $model): RedirectResponse
    {
        $this->authorize('is-super-admin');

        $model->update($request->validated());

        AuditService::logAction(
            action: "{$this->auditKey()}.updated",
            description: "{$this->label()} '{$model->name}' updated by super admin",
            auditable: $model,
        );

        return redirect()->route("{$this->routeBaseName()}.index")
            ->with('success', "{$this->label()} updated successfully.");
    }

    protected function removeResource(Model $model): RedirectResponse
    {
        $this->authorize('is-super-admin');

        if ($message = $this->deletionBlockedMessage($model)) {
            return back()->with('error', $message);
        }

        $name = $model->name;
        $model->delete();

        AuditService::logAction(
            action: "{$this->auditKey()}.deleted",
            description: "{$this->label()} '{$name}' deleted by super admin",
        );

        return redirect()->route("{$this->routeBaseName()}.index")
            ->with('success', "{$this->label()} deleted successfully.");
    }

    private function indexVariable(): string
    {
        return Str::camel(Str::pluralStudly(class_basename($this->modelClass())));
    }

    private function editVariable(Model $model): string
    {
        return Str::camel(class_basename($model));
    }
}
