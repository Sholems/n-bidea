@extends('layouts.dashboard')

@section('title', 'Business Owner Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Business Owner Dashboard</h1>
    <div>
        <a href="{{ route('business-owner.businesses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Create New Business
        </a>
        <a href="{{ route('business-owner.businesses.index') }}" class="btn btn-outline-secondary ms-2">
            View All Businesses
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md">
        <div class="card text-bg-primary h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Total Businesses</h6>
                <h2 class="mb-0">{{ $counts['total'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card text-bg-secondary h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Draft</h6>
                <h2 class="mb-0">{{ $counts['draft'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card text-bg-info h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Submitted</h6>
                <h2 class="mb-0">{{ $counts['submitted'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card text-bg-warning h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Awaiting Verification</h6>
                <h2 class="mb-0">{{ $counts['approved'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card text-bg-success h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Verified</h6>
                <h2 class="mb-0">{{ $counts['verified'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card text-bg-secondary h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Expired</h6>
                <h2 class="mb-0">{{ $counts['expired'] }}</h2>
            </div>
        </div>
    </div>
</div>

@if($pendingCorrections > 0)
<div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <div>
        You have <strong>{{ $pendingCorrections }}</strong> business(es) requiring corrections.
    </div>
</div>
@endif

@if($expiringBusinesses->isNotEmpty())
<div class="alert alert-danger d-flex align-items-start mb-4" role="alert">
    <i class="bi bi-clock-history me-2 mt-1"></i>
    <div class="flex-grow-1">
        <strong>Verification expiring soon</strong>
        <ul class="mb-0 mt-1">
            @foreach($expiringBusinesses as $expiring)
                <li>
                    <a href="{{ route('business-owner.businesses.show', $expiring) }}">{{ $expiring->business_name }}</a>
                    expires {{ $expiring->verification_expires_at->format('d M Y') }}
                    ({{ $expiring->verification_expires_at->diffForHumans() }})
                </li>
            @endforeach
        </ul>
        <a href="{{ route('business-owner.renewals.index') }}" class="btn btn-sm btn-danger mt-2">
            <i class="bi bi-arrow-repeat"></i> Request Renewal
        </a>
    </div>
</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Your Businesses</h5>
            </div>
            <div class="card-body p-0">
                @if($businesses->isNotEmpty())
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
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Business Name</th>
                                    <th>Sector</th>
                                    <th>Status</th>
                                    <th>Verification Expiry</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($businesses as $business)
                                <tr>
                                    <td>
                                        <a href="{{ route('business-owner.businesses.show', $business) }}">{{ $business->business_name }}</a>
                                    </td>
                                    <td>{{ $business->sector->name ?? '—' }}</td>
                                    <td><span class="badge bg-{{ $statusColors[$business->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $business->status)) }}</span></td>
                                    <td>{{ $business->verification_expires_at?->format('d M Y') ?? '—' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('business-owner.businesses.show', $business) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-building fs-1 text-muted"></i>
                    <h5 class="text-muted mt-3">No Businesses Yet</h5>
                    <p class="text-muted">Start by creating your first business application.</p>
                    <a href="{{ route('business-owner.businesses.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Create Your First Business
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person-vcard me-2"></i>Staff Clearance</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total staff</span>
                    <span class="fw-semibold">{{ $staffCounts['total'] }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Awaiting review</span>
                    <span class="fw-semibold">{{ $staffCounts['pending_review'] }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Correction required</span>
                    <span class="fw-semibold">{{ $staffCounts['correction_required'] }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Cleared</span>
                    <span class="fw-semibold text-success">{{ $staffCounts['approved'] }}</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Fees</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Unpaid</span>
                    <span class="fw-semibold">{{ $feeCounts['unpaid'] }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Awaiting confirmation</span>
                    <span class="fw-semibold">{{ $feeCounts['pending_confirmation'] }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Amount unpaid</span>
                    <span class="fw-semibold">₦{{ number_format($feeCounts['unpaid_amount'], 2) }}</span>
                </div>
                <a href="{{ route('business-owner.fees.index') }}" class="btn btn-sm btn-outline-primary w-100">View Fees</a>
            </div>
        </div>
    </div>
</div>
@endsection
