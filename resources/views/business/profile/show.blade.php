@extends('layouts.dashboard')

@section('title', 'Public Directory Listing')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Public Directory Listing</h1>
        <p class="text-muted mb-0">{{ $business->business_name }}</p>
    </div>
    <div class="d-flex gap-2">
        @if(in_array($business->status, ['approved', 'verified']))
            <a href="{{ route('business-owner.businesses.profile.edit', $business) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> {{ $business->profile ? 'Edit Listing' : 'Create Listing' }}
            </a>
        @endif
        <a href="{{ route('business-owner.businesses.show', $business) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Business Details
        </a>
    </div>
</div>

@if(! $business->profile)
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-shop fs-1 text-muted"></i>
            <h5 class="text-muted mt-3">No Public Listing Yet</h5>
            <p class="text-muted">Approved businesses receive a basic listing automatically. Add public services, locations, and trade interests to help partners understand your business.</p>
            @if(in_array($business->status, ['approved', 'verified']))
                <a href="{{ route('business-owner.businesses.profile.edit', $business) }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Create Listing
                </a>
            @else
                <span class="badge bg-secondary">Business must be verified first</span>
            @endif
        </div>
    </div>
@else
    @php
        $statusColors = [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
        ];
        $profile = $business->profile;
    @endphp

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-globe2 me-2"></i>Public Listing Content</h5>
                </div>
                <div class="card-body">
                    @if($profile->logo_path)
                        <div class="mb-4">
                            <small class="text-muted d-block mb-2">Business Logo</small>
                            <img src="{{ route('business-owner.businesses.profile.logo', $business) }}" alt="{{ $business->business_name }} logo" class="border rounded" style="width: 120px; height: 120px; object-fit: contain; background: #fff;">
                        </div>
                    @endif
                    <div class="mb-3">
                        <small class="text-muted d-block">Summary</small>
                        <p class="mb-0">{{ $profile->summary }}</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Services</small>
                        <p class="mb-0">{{ $profile->services }}</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Operating Locations</small>
                        <p class="mb-0">{{ $profile->operating_locations }}</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Trade Interests</small>
                        <p class="mb-0">{{ $profile->trade_interests ?? '—' }}</p>
                    </div>
                    <div>
                        <small class="text-muted d-block">Contact Preference</small>
                        <p class="mb-0">{{ $profile->contact_preference ?? 'NB-CCI controlled introduction' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Publication Status</h5>
                </div>
                <div class="card-body">
                    <span class="badge bg-{{ $statusColors[$profile->status] ?? 'secondary' }}">
                        {{ ucfirst($profile->status) }}
                    </span>
                    @if($profile->approved_at)
                        <div class="text-muted small mt-3">Approved {{ $profile->approved_at->format('d M Y') }}</div>
                    @endif
                    @if($profile->admin_note)
                        <hr>
                        <small class="text-muted d-block">Admin Note</small>
                        <p class="mb-0">{{ $profile->admin_note }}</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi {{ $business->is_verified ? 'bi-patch-check-fill' : 'bi-building-check' }} me-2"></i>{{ $business->is_verified ? 'Verified Business' : 'Approved Business' }}</h5>
                </div>
                <div class="card-body">
                    <small class="text-muted d-block">Registry Number</small>
                    <div class="fw-semibold mb-3">{{ $business->registry_number ?? '—' }}</div>
                    <small class="text-muted d-block">Sector</small>
                    <div class="fw-semibold mb-3">{{ $business->sector->name ?? '—' }}</div>
                    <small class="text-muted d-block">Location</small>
                    <div class="fw-semibold">{{ $business->city }}, {{ $business->state }}</div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
