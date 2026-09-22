@extends('layouts.dashboard')

@section('title', 'Manage Sectors')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Manage Sectors</h1>
    <a href="{{ route('super-admin.sectors.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Sector
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($sectors->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-grid fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Sectors Found</h5>
                <p class="text-muted">Get started by creating your first sector.</p>
                <a href="{{ route('super-admin.sectors.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Add Sector
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Businesses</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sectors as $sector)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $sector->name }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusColor = $sector->status === 'active' ? 'success' : ($sector->status === 'inactive' ? 'danger' : 'secondary');
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">{{ ucfirst($sector->status) }}</span>
                                </td>
                                <td>{{ $sector->businesses_count ?? 0 }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('super-admin.sectors.edit', $sector) }}" class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('super-admin.sectors.destroy', $sector) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this sector? This action cannot be undone.');">
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
    @if($sectors->hasPages())
        <div class="card-footer bg-white">
            {{ $sectors->links() }}
        </div>
    @endif
</div>
@endsection
