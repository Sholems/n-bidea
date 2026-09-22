@extends('layouts.dashboard')

@section('title', 'Manage Document Types')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Manage Document Types</h1>
    <a href="{{ route('super-admin.document-types.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Document Type
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($documentTypes->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-file-earmark fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Document Types Found</h5>
                <p class="text-muted">Get started by creating your first document type.</p>
                <a href="{{ route('super-admin.document-types.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Add Document Type
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Applies To</th>
                            <th>Required</th>
                            <th>Status</th>
                            <th>Documents</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documentTypes as $docType)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $docType->name }}</span>
                                </td>
                                <td>{{ $docType->description ?? '—' }}</td>
                                <td>{{ \App\Models\Business::COUNTRIES[$docType->country_code] ?? 'All businesses' }}</td>
                                <td>
                                    @if($docType->is_required)
                                        <span class="badge bg-danger">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColor = $docType->status === 'active' ? 'success' : ($docType->status === 'inactive' ? 'danger' : 'secondary');
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">{{ ucfirst($docType->status) }}</span>
                                </td>
                                <td>{{ $docType->documents_count ?? 0 }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('super-admin.document-types.edit', $docType) }}" class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('super-admin.document-types.destroy', $docType) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this document type? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if($documentTypes->hasPages())
        <div class="card-footer bg-white">
            {{ $documentTypes->links() }}
        </div>
    @endif
</div>
@endsection
