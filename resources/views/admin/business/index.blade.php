@extends('layouts.dashboard')

@section('title', 'Business Applications')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Business Applications</h1>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.businesses.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        @php
                            $statuses = ['draft', 'submitted', 'under_review', 'correction_required', 'approved', 'rejected', 'verified', 'expired', 'suspended'];
                        @endphp
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Business name, registration number..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Apply Filters
                    </button>
                </div>
            </div>
            @if(request()->hasAny(['queue', 'verification']))
                <input type="hidden" name="queue" value="{{ request('queue') }}">
                <input type="hidden" name="verification" value="{{ request('verification') }}">
                <div class="d-flex align-items-center gap-2 mt-3">
                    <span class="badge bg-light text-dark border">
                        {{ request('queue') === 'pending_review' ? 'Pending review queue' : ucfirst((string) request('verification')).' verification' }}
                    </span>
                    <a href="{{ route('admin.businesses.index') }}" class="small">Clear dashboard filter</a>
                </div>
            @endif
        </form>
    </div>
</div>

@php
    $statusColors = [
        'draft' => 'secondary',
        'submitted' => 'info',
        'under_review' => 'warning',
        'correction_required' => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
        'verified' => 'success',
        'expired' => 'secondary',
        'suspended' => 'danger',
    ];
@endphp

<div class="card">
    <div class="card-body p-0">
        @if($businesses->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-clipboard fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Applications Found</h5>
                <p class="text-muted mb-0">No business applications match your criteria.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Business Name</th>
                            <th>Owner</th>
                            <th>Registration No.</th>
                            <th>Status</th>
                            <th>Sector</th>
                            <th>State</th>
                            <th>Submitted</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($businesses as $business)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $business->business_name }}</span>
                                    @if($business->trading_name)
                                        <br><small class="text-muted">Trading as {{ $business->trading_name }}</small>
                                    @endif
                                </td>
                                <td>{{ $business->user->name ?? '—' }}</td>
                                <td>{{ $business->registration_number ?? '—' }}</td>
                                <td>
                                    @php
                                        $color = $statusColors[$business->status] ?? 'secondary';
                                        $extraClass = $business->status === 'correction_required' ? ' text-dark' : '';
                                    @endphp
                                    <span class="badge bg-{{ $color }}{{ $extraClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $business->status)) }}
                                    </span>
                                </td>
                                <td>{{ $business->sector->name ?? '—' }}</td>
                                <td>{{ $business->state ?? '—' }}</td>
                                <td>{{ $business->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.businesses.show', $business) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if($businesses->hasPages())
        <div class="card-footer bg-white">
            {{ $businesses->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
