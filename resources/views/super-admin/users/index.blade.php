@extends('layouts.dashboard')

@section('title', 'Manage Users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Manage Users</h1>
    <a href="{{ route('super-admin.users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add User
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('super-admin.users.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Name, email, or phone..." value="{{ request()->query('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="form-select">
                        <option value="">All Roles</option>
                        @foreach(['super_admin', 'admin', 'business_owner', 'government_official'] as $role)
                            <option value="{{ $role }}" {{ request()->query('role') === $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach(['active', 'suspended', 'pending'] as $status)
                            <option value="{{ $status }}" {{ request()->query('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

@php
    $roleBadgeColors = [
        'super_admin' => 'purple',
        'admin' => 'primary',
        'business_owner' => 'info',
        'government_official' => 'success',
    ];
    $statusBadgeColors = [
        'active' => 'success',
        'suspended' => 'danger',
        'pending' => 'warning',
    ];
@endphp

<div class="card">
    <div class="card-body p-0">
        @if($users->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-people fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Users Found</h5>
                <p class="text-muted">No users match your current filters.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $user->name }}</span>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? '—' }}</td>
                                <td>
                                    @php $color = $roleBadgeColors[$user->role] ?? 'secondary'; @endphp
                                    <span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                                </td>
                                <td>
                                    @php $color = $statusBadgeColors[$user->account_status] ?? 'secondary'; @endphp
                                    <span class="badge bg-{{ $color }}">{{ ucfirst($user->account_status) }}</span>
                                </td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('super-admin.users.show', $user) }}" class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('super-admin.users.edit', $user) }}" class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($user->account_status === 'active')
                                            <form action="{{ route('super-admin.users.suspend', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to suspend this user?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-outline-warning" title="Suspend">
                                                    <i class="bi bi-pause-circle"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('super-admin.users.activate', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to activate this user?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-outline-success" title="Activate">
                                                    <i class="bi bi-play-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('super-admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
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
    @if($users->hasPages())
        <div class="card-footer bg-white">
            {{ $users->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
