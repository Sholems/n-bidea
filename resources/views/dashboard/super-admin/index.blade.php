@extends('layouts.dashboard')

@section('title', 'Super Admin Dashboard')
@section('page-title', 'Super Admin Dashboard')

@section('content')
<div class="dashboard-heading">
    <div>
        <h1>Operations Overview</h1>
        <p class="text-muted mb-0">{{ now()->format('l, d F Y') }} · Review registry operations and platform readiness.</p>
    </div>
    <a href="{{ route('super-admin.reports.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-bar-chart me-1"></i> Reports
    </a>
</div>

<div class="metric-grid">
    <a href="{{ route('admin.businesses.index', ['verification' => 'active']) }}" class="metric-tile metric-tile--success">
        <div class="metric-tile__top"><span class="metric-tile__label">Active Verified</span><i class="bi bi-patch-check metric-tile__icon"></i></div>
        <div class="metric-tile__value">{{ $counts['total_verified'] }}</div>
        <small class="text-muted">{{ $counts['total_businesses'] }} total businesses</small>
    </a>
    <a href="{{ route('admin.businesses.index', ['queue' => 'pending_review']) }}" class="metric-tile metric-tile--attention">
        <div class="metric-tile__top"><span class="metric-tile__label">Applications to Review</span><i class="bi bi-clipboard-check metric-tile__icon"></i></div>
        <div class="metric-tile__value">{{ $counts['pending_applications'] }}</div>
        <small class="text-muted">Submitted or under review</small>
    </a>
    <a href="#awaiting-verification" class="metric-tile metric-tile--attention">
        <div class="metric-tile__top"><span class="metric-tile__label">Manual Verification</span><i class="bi bi-telephone metric-tile__icon"></i></div>
        <div class="metric-tile__value">{{ $reviewQueues['awaiting_verification'] }}</div>
        <small class="text-muted">Phone calls or site visits</small>
    </a>
    <a href="{{ route('admin.businesses.index', ['verification' => 'expiring_soon']) }}" class="metric-tile {{ $counts['expiring_soon'] > 0 ? 'metric-tile--danger' : '' }}">
        <div class="metric-tile__top"><span class="metric-tile__label">Expiring in 30 Days</span><i class="bi bi-clock-history metric-tile__icon"></i></div>
        <div class="metric-tile__value">{{ $counts['expiring_soon'] }}</div>
        <small class="text-muted">{{ $counts['renewal_requests'] }} renewal requests pending</small>
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Action Centre</h2>
                <span class="text-muted small">Oldest items shown first</span>
            </div>
            <div class="card-body p-0">
                @foreach($actionQueues as $queue)
                    <a href="{{ $queue['url'] }}" class="queue-row">
                        <span class="queue-row__icon"><i class="bi {{ $queue['icon'] }}"></i></span>
                        <span class="flex-grow-1">
                            <span class="fw-semibold d-block">{{ $queue['label'] }}</span>
                            <small class="text-muted">
                                {{ $queue['oldest_at'] ? 'Oldest item '.$queue['oldest_at']->diffForHumans() : 'Queue is clear' }}
                            </small>
                        </span>
                        <span class="badge {{ $queue['count'] > 0 ? 'bg-warning text-dark' : 'bg-light text-dark border' }} queue-row__count">{{ $queue['count'] }}</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Platform Setup</h2>
                <span class="badge bg-light text-dark border">{{ $setupChecklist->where('complete', true)->count() }}/{{ $setupChecklist->count() }}</span>
            </div>
            <div class="list-group list-group-flush">
                @foreach($setupChecklist as $item)
                    <a href="{{ $item['url'] }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3">
                        <i class="bi {{ $item['complete'] ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted' }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card h-100" id="awaiting-verification">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Awaiting Manual Verification</h2>
                <span class="badge bg-secondary">{{ $reviewQueues['awaiting_verification'] }}</span>
            </div>
            <div class="card-body p-0">
                @if($awaitingVerification->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead><tr><th>Business</th><th>Sector</th><th>Waiting Since</th><th class="text-end">Action</th></tr></thead>
                            <tbody>
                                @foreach($awaitingVerification as $business)
                                    <tr>
                                        <td><a href="{{ route('admin.businesses.show', $business) }}" class="fw-semibold text-decoration-none">{{ $business->business_name }}</a></td>
                                        <td>{{ $business->sector->name ?? 'Unclassified' }}</td>
                                        <td>{{ $business->approved_at ? \Carbon\Carbon::parse($business->approved_at)->format('d M Y') : $business->updated_at->format('d M Y') }}</td>
                                        <td class="text-end"><a href="{{ route('admin.businesses.show', $business) }}" class="btn btn-sm btn-primary">Review</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle fs-1 text-success"></i>
                        <p class="text-muted mt-2 mb-0">The manual verification queue is clear.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header"><h2 class="h5 mb-0">Verification Oversight</h2></div>
            <div class="card-body">
                <div class="d-flex justify-content-between py-2 border-bottom"><span>Expiring within 30 days</span><strong>{{ $verificationInsights['expiring_30_days'] }}</strong></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span>Expiring within 60 days</span><strong>{{ $verificationInsights['expiring_60_days'] }}</strong></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span>Expiring within 90 days</span><strong>{{ $verificationInsights['expiring_90_days'] }}</strong></div>
                <div class="d-flex justify-content-between py-2"><span>Not verified in last 30 days</span><strong>{{ $verificationInsights['not_verified_30_days'] }}</strong></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header"><h2 class="h5 mb-0">Configuration Status</h2></div>
            <div class="card-body">
                @foreach($systemChecks as $check)
                    <div class="d-flex align-items-start gap-2 {{ !$loop->last ? 'border-bottom pb-3 mb-3' : '' }}">
                        <span class="status-dot {{ $check['complete'] ? 'status-dot--ok' : 'status-dot--warning' }} mt-2"></span>
                        <div>
                            <div class="fw-semibold">{{ $check['label'] }}</div>
                            <small class="text-muted">{{ $check['detail'] }} · {{ $check['complete'] ? 'Configured' : 'Needs attention' }}</small>
                        </div>
                    </div>
                @endforeach
                <a href="{{ route('super-admin.settings.index') }}" class="btn btn-sm btn-outline-primary mt-3">Review Settings</a>
            </div>
        </div>
    </div>
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Recent Administrative Activity</h2>
                <a href="{{ route('super-admin.audit-logs.index') }}" class="btn btn-sm btn-outline-secondary">View Audit Log</a>
            </div>
            <div class="card-body p-0">
                @if($recentActivity->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead><tr><th>User</th><th>Activity</th><th>Date</th></tr></thead>
                            <tbody>
                                @foreach($recentActivity as $log)
                                    <tr><td>{{ $log->user->name ?? 'System' }}</td><td>{{ $log->description ?? str_replace('.', ' ', $log->action) }}</td><td class="text-nowrap">{{ $log->created_at->format('d M, H:i') }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">No administrative activity has been recorded.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card h-100"><div class="card-header"><h2 class="h6 mb-0">Businesses by Sector</h2></div><div class="card-body p-0">
            @forelse($reports['by_sector']->take(6) as $row)
                <div class="d-flex justify-content-between px-3 py-2 border-bottom"><span>{{ $row->sector->name ?? 'Unclassified' }}</span><strong>{{ $row->total }}</strong></div>
            @empty<div class="text-muted text-center py-4">No sector data yet.</div>@endforelse
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100"><div class="card-header"><h2 class="h6 mb-0">Businesses by State</h2></div><div class="card-body p-0">
            @forelse($reports['by_state']->take(6) as $row)
                <div class="d-flex justify-content-between px-3 py-2 border-bottom"><span>{{ $row->state ?? 'Unknown' }}</span><strong>{{ $row->total }}</strong></div>
            @empty<div class="text-muted text-center py-4">No location data yet.</div>@endforelse
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100"><div class="card-header"><h2 class="h6 mb-0">Recent Verification Checks</h2></div><div class="card-body p-0">
            @forelse($recentVerificationChecks->take(6) as $check)
                <a href="{{ route('admin.businesses.show', $check->business) }}" class="queue-row">
                    <span class="flex-grow-1"><span class="fw-semibold d-block">{{ $check->business->business_name }}</span><small class="text-muted">{{ $check->method === 'site_visit' ? 'Site visit' : 'Phone call' }} · {{ $check->checked_at->format('d M Y') }}</small></span>
                    <span class="badge {{ $check->decision === 'verified' ? 'bg-success' : 'bg-secondary' }}">{{ $check->decision === 'verified' ? 'Verified' : 'Not verified' }}</span>
                </a>
            @empty<div class="text-muted text-center py-4">No checks recorded yet.</div>@endforelse
        </div></div>
    </div>
</div>
@endsection
