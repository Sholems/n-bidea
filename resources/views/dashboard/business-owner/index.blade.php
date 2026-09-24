@extends('layouts.dashboard')

@section('title', 'Business Owner Dashboard')
@section('page-title', 'Business Owner Dashboard')

@section('content')
<div class="dashboard-heading">
    <div>
        <h1>Welcome, {{ $user->name }}</h1>
        <p class="text-muted mb-0">Track each registration, verification, public profile, staff record, and payment.</p>
    </div>
    <a href="{{ route('business-owner.businesses.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Register a Business</a>
</div>

@if($businesses->isEmpty())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
            <div>
                <span class="section-kicker">Get Started</span>
                <h2 class="h4 mt-2">Register your first business</h2>
                <p class="text-muted mb-0">Create the business record, upload required documents, and submit it for NB-CCI review.</p>
            </div>
            <a href="{{ route('business-owner.businesses.create') }}" class="btn btn-primary flex-shrink-0">Start Registration</a>
        </div>
    </div>
@else
    <div class="metric-grid">
        <a href="{{ route('business-owner.businesses.index') }}" class="metric-tile">
            <div class="metric-tile__top"><span class="metric-tile__label">My Businesses</span><i class="bi bi-building metric-tile__icon"></i></div>
            <div class="metric-tile__value">{{ $counts['total'] }}</div>
            <small class="text-muted">All registered records</small>
        </a>
        <a href="#next-actions" class="metric-tile {{ $pendingCorrections > 0 ? 'metric-tile--danger' : 'metric-tile--attention' }}">
            <div class="metric-tile__top"><span class="metric-tile__label">Needs My Action</span><i class="bi bi-list-check metric-tile__icon"></i></div>
            <div class="metric-tile__value">{{ $actionItems->whereIn('priority', ['urgent', 'action'])->count() }}</div>
            <small class="text-muted">Corrections, documents, or profile setup</small>
        </a>
        <a href="{{ route('business-owner.businesses.index') }}" class="metric-tile metric-tile--attention">
            <div class="metric-tile__top"><span class="metric-tile__label">Awaiting Verification</span><i class="bi bi-hourglass-split metric-tile__icon"></i></div>
            <div class="metric-tile__value">{{ $counts['approved'] }}</div>
            <small class="text-muted">Registration approved</small>
        </a>
        <a href="{{ route('public.directory.index') }}" class="metric-tile metric-tile--success">
            <div class="metric-tile__top"><span class="metric-tile__label">Active Verified</span><i class="bi bi-patch-check metric-tile__icon"></i></div>
            <div class="metric-tile__value">{{ $counts['verified'] }}</div>
            <small class="text-muted">Currently valid verification</small>
        </a>
    </div>
@endif

@if($expiringBusinesses->isNotEmpty())
    <div class="alert alert-danger d-flex align-items-start gap-3 mb-4">
        <i class="bi bi-clock-history fs-4"></i>
        <div class="flex-grow-1">
            <strong>Verification expiring soon</strong>
            @foreach($expiringBusinesses as $expiring)
                <div class="mt-1"><a href="{{ route('business-owner.businesses.show', $expiring) }}">{{ $expiring->business_name }}</a> expires {{ $expiring->verification_expires_at->format('d M Y') }}.</div>
            @endforeach
        </div>
        <a href="{{ route('business-owner.renewals.index') }}" class="btn btn-sm btn-danger">Request Renewal</a>
    </div>
@endif

@if($businesses->isNotEmpty())
<div class="row g-4 mb-4">
    <div class="col-xl-7">
        <div class="card h-100" id="next-actions">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Next Actions</h2>
                <span class="badge bg-light text-dark border">{{ $actionItems->count() }}</span>
            </div>
            <div class="card-body p-0">
                @forelse($actionItems as $item)
                    <a href="{{ $item['url'] }}" class="queue-row">
                        <span class="queue-row__icon"><i class="bi {{ $item['priority'] === 'urgent' ? 'bi-exclamation-triangle' : ($item['priority'] === 'action' ? 'bi-arrow-right-circle' : 'bi-hourglass-split') }}"></i></span>
                        <span class="flex-grow-1">
                            <span class="fw-semibold d-block">{{ $item['business']->business_name }}</span>
                            <span>{{ $item['label'] }}</span>
                            <small class="text-muted d-block">{{ $item['description'] }}</small>
                        </span>
                        <span class="badge {{ $item['priority'] === 'urgent' ? 'bg-danger' : ($item['priority'] === 'action' ? 'bg-warning text-dark' : 'bg-light text-dark border') }}">{{ ucfirst($item['priority']) }}</span>
                    </a>
                @empty
                    <div class="text-center py-5"><i class="bi bi-check-circle fs-1 text-success"></i><p class="text-muted mt-2 mb-0">No action is required right now.</p></div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center"><h2 class="h5 mb-0">Staff Clearance</h2><span class="fw-semibold">{{ $staffCounts['total'] }}</span></div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Awaiting review</span><strong>{{ $staffCounts['pending_review'] }}</strong></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Correction required</span><strong class="text-danger">{{ $staffCounts['correction_required'] }}</strong></div>
                <div class="d-flex justify-content-between"><span class="text-muted">Cleared</span><strong class="text-success">{{ $staffCounts['approved'] }}</strong></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"><h2 class="h5 mb-0">Fees</h2><i class="bi bi-credit-card"></i></div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Unpaid invoices</span><strong>{{ $feeCounts['unpaid'] }}</strong></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Awaiting confirmation</span><strong>{{ $feeCounts['pending_confirmation'] }}</strong></div>
                <div class="d-flex justify-content-between mb-3"><span class="text-muted">Amount unpaid</span><strong>₦{{ number_format($feeCounts['unpaid_amount'], 2) }}</strong></div>
                <a href="{{ route('business-owner.fees.index') }}" class="btn btn-sm btn-outline-primary w-100">Review Fees</a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="h5 mb-0">Business Portfolio</h2>
        <a href="{{ route('business-owner.businesses.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Business</th><th>Status</th><th>Directory Profile</th><th>Verification Expiry</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                    @foreach($businesses->take(10) as $business)
                        @php
                            $statusColors = ['draft' => 'secondary', 'submitted' => 'info', 'under_review' => 'warning', 'correction_required' => 'warning', 'approved' => 'primary', 'rejected' => 'danger', 'verified' => 'success', 'expired' => 'secondary', 'suspended' => 'danger'];
                        @endphp
                        <tr>
                            <td><a href="{{ route('business-owner.businesses.show', $business) }}" class="fw-semibold text-decoration-none">{{ $business->business_name }}</a><small class="d-block text-muted">{{ $business->sector->name ?? 'Unclassified' }}</small></td>
                            <td><span class="badge bg-{{ $statusColors[$business->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $business->status)) }}</span></td>
                            <td>{{ $business->profile ? ucfirst($business->profile->status) : 'Not created' }}</td>
                            <td>{{ $business->verification_expires_at?->format('d M Y') ?? '—' }}</td>
                            <td class="text-end"><a href="{{ route('business-owner.businesses.show', $business) }}" class="btn btn-sm btn-outline-primary">Open</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
