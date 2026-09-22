@extends('layouts.dashboard')

@section('title', 'Directory Listings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Directory Listings</h1>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.business-profiles.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach(['pending', 'approved', 'rejected'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Business name or registry number" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@php
    $statusColors = [
        'pending' => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
    ];
@endphp

<div class="card">
    <div class="card-body p-0">
        @if($profiles->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-shop fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Profiles Found</h5>
                <p class="text-muted mb-0">No directory listing submissions match your filters.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Business</th>
                            <th>Sector</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Approved By</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($profiles as $profile)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $profile->business->business_name }}</span>
                                    <br><small class="text-muted">{{ $profile->business->registry_number }}</small>
                                </td>
                                <td>{{ $profile->business->sector->name ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ $statusColors[$profile->status] ?? 'secondary' }}">
                                        {{ ucfirst($profile->status) }}
                                    </span>
                                </td>
                                <td>{{ $profile->created_at->format('d M Y') }}</td>
                                <td>{{ $profile->approver->name ?? '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.business-profiles.show', $profile) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Review
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if($profiles->hasPages())
        <div class="card-footer bg-white">
            {{ $profiles->links() }}
        </div>
    @endif
</div>
@endsection
