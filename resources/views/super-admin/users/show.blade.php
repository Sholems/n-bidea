@extends('layouts.dashboard')

@section('title', 'User Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">User Details</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('super-admin.users.edit', $user) }}" class="btn btn-outline-secondary">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
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

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person me-2"></i>User Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Full Name</label>
                        <div class="fw-semibold">{{ $user->name }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Email Address</label>
                        <div class="fw-semibold">{{ $user->email }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Phone Number</label>
                        <div class="fw-semibold">{{ $user->phone ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Agency</label>
                        <div class="fw-semibold">{{ $user->agency->name ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Status</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted small">Role</label>
                    <div>
                        @php $color = $roleBadgeColors[$user->role] ?? 'secondary'; @endphp
                        <span class="badge bg-{{ $color }} fs-6">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Account Status</label>
                    <div>
                        @php $color = $statusBadgeColors[$user->account_status] ?? 'secondary'; @endphp
                        <span class="badge bg-{{ $color }} fs-6">{{ ucfirst($user->account_status) }}</span>
                    </div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Created: {{ $user->created_at->format('d M Y, h:i A') }}</small>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Last Updated: {{ $user->updated_at->format('d M Y, h:i A') }}</small>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Account Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($user->account_status === 'active')
                        <form action="{{ route('super-admin.users.suspend', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to suspend this user?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="bi bi-pause-circle me-2"></i> Suspend Account
                            </button>
                        </form>
                    @else
                        <form action="{{ route('super-admin.users.activate', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to activate this user?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-play-circle me-2"></i> Activate Account
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('super-admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash me-2"></i> Delete Account
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
