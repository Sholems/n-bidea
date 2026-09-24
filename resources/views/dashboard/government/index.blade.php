@extends('layouts.dashboard')

@section('title', 'Government Official Dashboard')
@section('page-title', 'Government Official Dashboard')

@section('content')
<div class="dashboard-heading">
    <div>
        <h1>Verification Workspace</h1>
        <p class="text-muted mb-0">{{ auth()->user()->agency->name ?? 'Government Official' }} · Search authorized business and staff records.</p>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body p-4">
        <form action="{{ route('government.search.execute') }}" method="POST">
            @csrf
            <label for="dashboard_query" class="form-label fw-semibold">Business and staff search</label>
            <div class="input-group input-group-lg">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="search" name="query" id="dashboard_query" class="form-control" placeholder="Registry number, business name, staff number, NIN, or passport number" maxlength="255" required autofocus>
                <button type="submit" class="btn btn-primary px-4">Search Records</button>
            </div>
            <div class="form-text">Every sensitive record view is recorded in the audit trail.</div>
        </form>
    </div>
</div>

<div class="metric-grid">
    <div class="metric-tile metric-tile--success">
        <div class="metric-tile__top"><span class="metric-tile__label">Checks Recorded Today</span><i class="bi bi-shield-check metric-tile__icon"></i></div>
        <div class="metric-tile__value">{{ $counts['checks_today'] }}</div>
        <small class="text-muted">Explicit verification records</small>
    </div>
    <div class="metric-tile">
        <div class="metric-tile__top"><span class="metric-tile__label">Checks This Week</span><i class="bi bi-calendar-week metric-tile__icon"></i></div>
        <div class="metric-tile__value">{{ $counts['checks_this_week'] }}</div>
        <small class="text-muted">Since {{ now()->startOfWeek()->format('d M') }}</small>
    </div>
    <div class="metric-tile">
        <div class="metric-tile__top"><span class="metric-tile__label">Lookups Today</span><i class="bi bi-eye metric-tile__icon"></i></div>
        <div class="metric-tile__value">{{ $counts['lookups_today'] }}</div>
        <small class="text-muted">Business and staff records viewed</small>
    </div>
    <div class="metric-tile">
        <div class="metric-tile__top"><span class="metric-tile__label">All Recorded Checks</span><i class="bi bi-journal-check metric-tile__icon"></i></div>
        <div class="metric-tile__value">{{ $counts['checks_total'] }}</div>
        <small class="text-muted">{{ $counts['lookups_total'] }} total lookups</small>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="h5 mb-0">Recent Verification Activity</h2>
        <a href="{{ route('government.search') }}" class="btn btn-sm btn-outline-primary">Advanced Search</a>
    </div>
    <div class="card-body p-0">
        @if($recentActivity->isNotEmpty())
            @php
                $actionLabels = [
                    'government_verification_check' => ['label' => 'Check recorded', 'color' => 'success', 'icon' => 'bi-shield-check'],
                    'government_view_business' => ['label' => 'Business lookup', 'color' => 'info', 'icon' => 'bi-building'],
                    'government_view_staff' => ['label' => 'Staff lookup', 'color' => 'primary', 'icon' => 'bi-person-badge'],
                ];
            @endphp
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>Record</th><th>Activity</th><th>Date</th><th class="text-end">Action</th></tr></thead>
                    <tbody>
                        @foreach($recentActivity as $activity)
                            @php
                                $meta = $actionLabels[$activity->action] ?? ['label' => $activity->action, 'color' => 'secondary', 'icon' => 'bi-clock-history'];
                                $recordUrl = $activity->auditable instanceof \App\Models\Business
                                    ? route('government.businesses.show', $activity->auditable)
                                    : ($activity->auditable instanceof \App\Models\StaffMember ? route('government.staff.show', $activity->auditable) : null);
                            @endphp
                            <tr>
                                <td>{{ $activity->description ?? 'Verification activity' }}</td>
                                <td><span class="badge bg-{{ $meta['color'] }}"><i class="bi {{ $meta['icon'] }} me-1"></i>{{ $meta['label'] }}</span></td>
                                <td class="text-nowrap">{{ $activity->created_at->format('d M Y, H:i') }}</td>
                                <td class="text-end">@if($recordUrl)<a href="{{ $recordUrl }}" class="btn btn-sm btn-outline-primary">Open</a>@else<span class="text-muted">—</span>@endif</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-search fs-1 text-muted"></i>
                <p class="text-muted mt-3 mb-0">Search for a business or staff member to begin.</p>
            </div>
        @endif
    </div>
</div>
@endsection
