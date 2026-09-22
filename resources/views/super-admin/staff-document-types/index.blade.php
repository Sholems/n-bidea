@extends('layouts.dashboard')

@section('title', 'Staff Document Types')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Staff Document Types</h1>
    <a href="{{ route('super-admin.staff-document-types.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Staff Document Type
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($staffDocumentTypes->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-file-earmark fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Staff Document Types Found</h5>
                <p class="text-muted mb-0">Staff cannot be submitted for review until required document types exist.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Required</th>
                            <th>Status</th>
                            <th>Documents</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staffDocumentTypes as $type)
                            <tr>
                                <td class="fw-semibold">{{ $type->name }}</td>
                                <td>{{ $type->description ?? '—' }}</td>
                                <td><span class="badge bg-{{ $type->is_required ? 'danger' : 'secondary' }}">{{ $type->is_required ? 'Yes' : 'No' }}</span></td>
                                <td><span class="badge bg-{{ $type->status === 'active' ? 'success' : 'danger' }}">{{ ucfirst($type->status) }}</span></td>
                                <td>{{ $type->documents_count }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('super-admin.staff-document-types.edit', $type) }}" class="btn btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                        <form action="{{ route('super-admin.staff-document-types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this staff document type?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
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
    @if($staffDocumentTypes->hasPages())
        <div class="card-footer bg-white">{{ $staffDocumentTypes->links() }}</div>
    @endif
</div>
@endsection
