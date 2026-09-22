<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicationRequest;
use App\Models\ContentCategory;
use App\Models\Publication;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SuperAdminPublicationController extends Controller
{
    public function index(): View
    {
        $this->authorize('is-super-admin');

        $publications = Publication::with('category', 'uploader')->latest()->paginate(20);

        return view('super-admin.publications.index', compact('publications'));
    }

    public function create(): View
    {
        $this->authorize('is-super-admin');

        $categories = ContentCategory::where('status', 'active')->orderBy('name')->get();

        return view('super-admin.publications.create', compact('categories'));
    }

    public function store(PublicationRequest $request): RedirectResponse
    {
        $this->authorize('is-super-admin');

        $validated = $request->validated();
        $file = $request->file('file');
        $path = $file->store('publications', 'private');

        unset($validated['file']);

        $publication = Publication::create($validated + [
            'uploaded_by' => auth()->id(),
            'file_path' => $path,
            'original_file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        AuditService::logAction(
            action: 'publication.created',
            description: "Publication '{$publication->title}' created by super admin",
            auditable: $publication,
        );

        return redirect()->route('super-admin.publications.index')
            ->with('success', 'Publication saved successfully.');
    }

    public function show(Publication $publication): View
    {
        $this->authorize('is-super-admin');

        return view('super-admin.publications.show', compact('publication'));
    }

    public function edit(Publication $publication): View
    {
        $this->authorize('is-super-admin');

        $categories = ContentCategory::where('status', 'active')->orderBy('name')->get();

        return view('super-admin.publications.edit', compact('publication', 'categories'));
    }

    public function update(PublicationRequest $request, Publication $publication): RedirectResponse
    {
        $this->authorize('is-super-admin');

        $validated = $request->validated();

        $previousPath = $publication->file_path;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $validated['file_path'] = $file->store('publications', 'private');
            $validated['original_file_name'] = $file->getClientOriginalName();
            $validated['mime_type'] = $file->getMimeType();
            $validated['file_size'] = $file->getSize();
        }

        unset($validated['file']);

        $publication->update($validated);

        if ($request->hasFile('file') && $previousPath && $previousPath !== $publication->file_path) {
            Storage::disk('private')->delete($previousPath);
        }

        AuditService::logAction(
            action: 'publication.updated',
            description: "Publication '{$publication->title}' updated by super admin",
            auditable: $publication,
        );

        return redirect()->route('super-admin.publications.index')
            ->with('success', 'Publication updated successfully.');
    }

    public function destroy(Publication $publication): RedirectResponse
    {
        $this->authorize('is-super-admin');

        $title = $publication->title;
        $filePath = $publication->file_path;

        $publication->delete();

        if ($filePath) {
            Storage::disk('private')->delete($filePath);
        }

        AuditService::logAction(
            action: 'publication.deleted',
            description: "Publication '{$title}' deleted by super admin",
        );

        return redirect()->route('super-admin.publications.index')
            ->with('success', 'Publication deleted successfully.');
    }
}
