<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessReportRequest;
use App\Models\Business;
use App\Models\Sector;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SuperAdminReportController extends Controller
{
    public function index(): View
    {
        $this->authorize('is-super-admin');

        $sectors = Sector::orderBy('name')->get();
        $states = $this->states();

        return view('super-admin.reports.index', compact('sectors', 'states'));
    }

    public function generate(BusinessReportRequest $request): View
    {
        $this->authorize('is-super-admin');

        $validated = $request->validated();

        $query = Business::with(['sector', 'user']);

        if (! empty($validated['sector_id'])) {
            $query->where('sector_id', $validated['sector_id']);
        }

        if (! empty($validated['state'])) {
            $query->where('state', $validated['state']);
        }

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (! empty($validated['date_from'])) {
            $query->where('created_at', '>=', $validated['date_from']);
        }

        if (! empty($validated['date_to'])) {
            $query->where('created_at', '<=', $validated['date_to'].' 23:59:59');
        }

        $businesses = $query->latest()->paginate(50)->withQueryString();

        $sectors = Sector::orderBy('name')->get();
        $states = $this->states();

        $filters = $validated;

        return view('super-admin.reports.show', compact('businesses', 'sectors', 'states', 'filters'));
    }

    /**
     * @return Collection<int, string>
     */
    private function states(): Collection
    {
        return Business::query()
            ->whereNotNull('state')
            ->distinct()
            ->orderBy('state')
            ->pluck('state');
    }
}
