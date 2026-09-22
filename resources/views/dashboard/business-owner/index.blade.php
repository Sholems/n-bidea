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
                <h6 class="card-title text-uppercase opacity-75">Draft Applications</h6>
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
        <div class="card text-bg-success h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Verified</h6>
                <h2 class="mb-0">{{ $counts['verified'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card text-bg-warning h-100">
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

@if($latestBusiness)
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Latest Business</h5>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h5>{{ $latestBusiness->business_name }}</h5>
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
                    $color = $statusColors[$latestBusiness->status] ?? 'secondary';
                @endphp
                <p class="mb-1">
                    Status: <span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $latestBusiness->status)) }}</span>
                </p>
                @if($latestBusiness->verification_expires_at)
                <p class="text-muted mb-0">
                    Verification Expiry: {{ $latestBusiness->verification_expires_at->format('d M Y') }}
                </p>
                @endif
            </div>
            <a href="{{ route('business-owner.businesses.show', $latestBusiness) }}" class="btn btn-outline-primary">
                View Details
            </a>
        </div>
    </div>
</div>
@else
<div class="card mb-4">
    <div class="card-body text-center py-5">
        <i class="bi bi-building fs-1 text-muted"></i>
        <h5 class="text-muted mt-3">No Businesses Yet</h5>
        <p class="text-muted">Start by creating your first business application.</p>
        <a href="{{ route('business-owner.businesses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Create Your First Business
        </a>
    </div>
</div>
@endif
@endsection