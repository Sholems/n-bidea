@extends('layouts.dashboard')

@section('title', 'Manage Agencies')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Manage Agencies</h1>
    <a href="{{ route('super-admin.agencies.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Agency
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($agencies->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-building fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Agencies Found</h5>
                <p class="text-muted">Get started by creating your first agency.</p>
                <a href="{{ route('super-admin.agencies.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Add Agency
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Contact Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Users</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agencies as $agency)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $agency->name }}</span>
                                </td>
                                <td>{{ ucfirst(str_replace('_', ' ', $agency->type ?? '—')) }}</td>
                                <td>{{ $agency->contact_email ?? '—' }}</td>
                                <td>{{ $agency->contact_phone ?? '—' }}</td>
                                <td>
                                    @php
                                        $statusColor = $agency->status === 'active' ? 'success' : ($agency->status === 'inactive' ? 'danger' : 'secondary');
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">{{ ucfirst($agency->status) }}</span>
                                </td>
                                <td>{{ $agency->users_count ?? 0 }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('super-admin.agencies.edit', $agency) }}" class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('super-admin.agencies.destroy', $agency) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this agency? This action cannot be undone.');">
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
    @if($agencies->hasPages())
        <div class="card-footer bg-white">
            {{ $agencies->links() }}
        </div>
    @endif
</div>
@endsection
