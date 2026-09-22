@extends('layouts.dashboard')

@section('title', 'Manage Publications')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Manage Publications</h1>
    <a href="{{ route('super-admin.publications.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Publication
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($publications->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-file-earmark-richtext fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Publications Found</h5>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Downloads</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($publications as $publication)
                            <tr>
                                <td class="fw-semibold">{{ $publication->title }}</td>
                                <td>{{ $publication->category?->name ?? '-' }}</td>
                                <td><span class="badge bg-{{ $publication->status === 'published' ? 'success' : 'secondary' }}">{{ ucfirst($publication->status) }}</span></td>
                                <td>{{ $publication->download_count }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('super-admin.publications.show', $publication) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('super-admin.publications.edit', $publication) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                        <form action="{{ route('super-admin.publications.destroy', $publication) }}" method="POST" onsubmit="return confirm('Delete this publication?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
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
    @if($publications->hasPages())
        <div class="card-footer bg-white">{{ $publications->links() }}</div>
    @endif
</div>
@endsection
