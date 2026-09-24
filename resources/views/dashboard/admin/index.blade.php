@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')
<div class="dashboard-heading">
    <div>
        <h1>Review Desk</h1>
        <p class="text-muted mb-0">Prioritize registry reviews and operational queues by waiting time.</p>
    </div>
    <a href="{{ route('admin.businesses.index', ['queue' => 'pending_review']) }}" class="btn btn-primary">
        <i class="bi bi-clipboard-check me-1"></i> Review Applications
    </a>
</div>

<div class="metric-grid">
    <a href="{{ route('admin.businesses.index', ['queue' => 'pending_review']) }}" class="metric-tile metric-tile--attention">
        <div class="metric-tile__top"><span class="metric-tile__label">Applications Pending</span><i class="bi bi-inbox metric-tile__icon"></i></div>
        <div class="metric-tile__value">{{ $counts['submitted'] + $counts['under_review'] }}</div>
        <small class="text-muted">Submitted and under review</small>
    </a>
    <a href="{{ route('admin.businesses.index', ['status' => 'correction_required']) }}" class="metric-tile {{ $counts['correction_required'] > 0 ? 'metric-tile--danger' : '' }}">
        <div class="metric-tile__top"><span class="metric-tile__label">Corrections Outstanding</span><i class="bi bi-exclamation-circle metric-tile__icon"></i></div>
        <div class="metric-tile__value">{{ $counts['correction_required'] }}</div>
        <small class="text-muted">Waiting for owner response</small>
    </a>
    <a href="{{ route('admin.businesses.index', ['verification' => 'active']) }}" class="metric-tile metric-tile--success">
        <div class="metric-tile__top"><span class="metric-tile__label">Active Verified</span><i class="bi bi-patch-check metric-tile__icon"></i></div>
        <div class="metric-tile__value">{{ $counts['verified'] }}</div>
        <small class="text-muted">Currently valid verification</small>
    </a>
    <a href="{{ route('admin.businesses.index', ['verification' => 'expired']) }}" class="metric-tile {{ $counts['expired'] > 0 ? 'metric-tile--danger' : '' }}">
        <div class="metric-tile__top"><span class="metric-tile__label">Expired</span><i class="bi bi-clock-history metric-tile__icon"></i></div>
        <div class="metric-tile__value">{{ $counts['expired'] }}</div>
        <small class="text-muted">Renewal may be required</small>
    </a>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="h5 mb-0">Operational Queues</h2>
        <span class="text-muted small">Select a queue to continue work</span>
    </div>
    <div class="row g-0">
        @foreach($actionQueues as $queue)
            <div class="col-md-6 col-xl-4 border-bottom">
                <a href="{{ $queue['url'] }}" class="queue-row h-100">
                    <span class="queue-row__icon"><i class="bi {{ $queue['icon'] }}"></i></span>
                    <span class="flex-grow-1">
                        <span class="fw-semibold d-block">{{ $queue['label'] }}</span>
                        <small class="text-muted">{{ $queue['oldest_at'] ? 'Oldest '.$queue['oldest_at']->diffForHumans() : 'Queue is clear' }}</small>
                    </span>
                    <span class="badge {{ $queue['count'] > 0 ? 'bg-warning text-dark' : 'bg-light text-dark border' }} queue-row__count">{{ $queue['count'] }}</span>
                </a>
            </div>
        @endforeach
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Priority Application Reviews</h2>
                <span class="badge bg-secondary">{{ $pendingReviews->count() }}</span>
            </div>
            <div class="card-body p-0">
                @if($pendingReviews->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead><tr><th>Business</th><th>Sector</th><th>Waiting</th><th class="text-end">Action</th></tr></thead>
                            <tbody>
                                @foreach($pendingReviews as $review)
                                    <tr>
                                        <td><a href="{{ route('admin.businesses.show', $review) }}" class="fw-semibold text-decoration-none">{{ $review->business_name }}</a><small class="d-block text-muted">{{ $review->user->name ?? 'Unknown owner' }}</small></td>
                                        <td>{{ $review->sector->name ?? 'Unclassified' }}</td>
                                        <td>{{ $review->created_at->diffForHumans(null, true) }}</td>
                                        <td class="text-end"><a href="{{ route('admin.businesses.show', $review) }}" class="btn btn-sm btn-primary">Review</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5"><i class="bi bi-check-circle fs-1 text-success"></i><p class="text-muted mt-2 mb-0">All application reviews are current.</p></div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header"><h2 class="h5 mb-0">Recent Submitted Applications</h2></div>
            <div class="card-body p-0">
                @forelse($recentSubmissions->take(8) as $submission)
                    <a href="{{ route('admin.businesses.show', $submission) }}" class="queue-row">
                        <span class="flex-grow-1">
                            <span class="fw-semibold d-block">{{ $submission->business_name }}</span>
                            <small class="text-muted">{{ $submission->sector->name ?? 'Unclassified' }} · {{ $submission->created_at->format('d M Y') }}</small>
                        </span>
                        <span class="badge {{ $submission->status === 'submitted' ? 'bg-info text-dark' : 'bg-warning text-dark' }}">{{ ucfirst(str_replace('_', ' ', $submission->status)) }}</span>
                    </a>
                @empty
                    <div class="text-center py-5 text-muted">No submitted applications yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
