@extends('layouts.dashboard')

@section('title', 'Super Admin Dashboard')

@section('content')
<h1 class="h3 mb-4">Super Admin Dashboard</h1>

@if($reviewQueues['awaiting_verification'] > 0)
<div class="alert alert-warning d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="d-flex align-items-center">
        <i class="bi bi-patch-exclamation-fill fs-4 me-3"></i>
        <div>
            <strong>{{ $reviewQueues['awaiting_verification'] }}</strong>
            business(es) approved and waiting for your phone call or site visit before they can be marked verified.
        </div>
    </div>
    <a href="#awaiting-verification" class="btn btn-warning btn-sm">Review Now</a>
</div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md">
        <div class="card text-bg-primary h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Total Users</h6>
                <h2 class="mb-0">{{ $counts['total_users'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card text-bg-info h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Total Businesses</h6>
                <h2 class="mb-0">{{ $counts['total_businesses'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card text-bg-success h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Verified</h6>
                <h2 class="mb-0">{{ $counts['total_verified'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card text-bg-secondary h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Officials</h6>
                <h2 class="mb-0">{{ $counts['total_officials'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md">
        <div class="card text-bg-secondary h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Admins</h6>
                <h2 class="mb-0">{{ $counts['total_admins'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card text-bg-warning h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Pending Applications</h6>
                <h2 class="mb-0">{{ $counts['pending_applications'] }}</h2>
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
    <div class="col-md">
        <div class="card text-bg-danger h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Renewals Pending</h6>
                <h2 class="mb-0">{{ $counts['renewal_requests'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md">
        <a href="#awaiting-verification" class="card text-bg-warning h-100 text-decoration-none text-reset">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Awaiting Verification</h6>
                <h2 class="mb-0">{{ $reviewQueues['awaiting_verification'] }}</h2>
            </div>
        </a>
    </div>
    <div class="col-md">
        <a href="{{ route('admin.business-profiles.index') }}" class="card text-bg-warning h-100 text-decoration-none text-reset">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Directory Profiles Pending</h6>
                <h2 class="mb-0">{{ $reviewQueues['profiles_pending_review'] }}</h2>
            </div>
        </a>
    </div>
    <div class="col-md">
        <a href="{{ route('admin.staff.index') }}" class="card text-bg-warning h-100 text-decoration-none text-reset">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Staff Awaiting Review</h6>
                <h2 class="mb-0">{{ $reviewQueues['staff_pending_review'] }}</h2>
            </div>
        </a>
    </div>
    <div class="col-md">
        <a href="{{ route('admin.fees.index') }}" class="card text-bg-warning h-100 text-decoration-none text-reset">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Fees Awaiting Confirmation</h6>
                <h2 class="mb-0">{{ $reviewQueues['fees_pending_confirmation'] }}</h2>
            </div>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card h-100" id="awaiting-verification">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Awaiting Your Verification</h5>
                <span class="badge bg-secondary">{{ $reviewQueues['awaiting_verification'] }}</span>
            </div>
            <div class="card-body p-0">
                @if($awaitingVerification->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Business Name</th>
                                <th>Sector</th>
                                <th>Approved</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($awaitingVerification as $business)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.businesses.show', $business) }}">{{ $business->business_name }}</a>
                                </td>
                                <td>{{ $business->sector->name ?? '—' }}</td>
                                <td>{{ $business->updated_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.businesses.show', $business) }}" class="btn btn-sm btn-warning">Verify</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-check-circle fs-1 text-success"></i>
                    <p class="text-muted mt-2 mb-0">Nothing awaiting verification.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Recent Verification Checks</h5>
            </div>
            <div class="card-body p-0">
                @if($recentVerificationChecks->isNotEmpty())
                <ul class="list-group list-group-flush">
                    @foreach($recentVerificationChecks as $check)
                    <li class="list-group-item px-3">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.businesses.show', $check->business) }}" class="text-decoration-none">{{ $check->business->business_name }}</a>
                            <span class="badge {{ $check->decision === 'verified' ? 'bg-success' : 'bg-secondary' }}">{{ $check->decision === 'verified' ? 'Verified' : 'Not verified' }}</span>
                        </div>
                        <small class="text-muted">{{ $check->method === 'site_visit' ? 'Site visit' : 'Phone call' }} by {{ $check->superAdmin->name ?? 'Super Admin' }} · {{ $check->checked_at->format('d M Y') }}</small>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-telephone fs-1 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">No verification checks recorded yet.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Recent Activity Logs</h5>
            </div>
            <div class="card-body p-0">
                @if($recentActivity->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Details</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentActivity as $log)
                            <tr>
                                <td>{{ $log->user->name ?? 'System' }}</td>
                                <td><span class="badge bg-secondary">{{ $log->action }}</span></td>
                                <td>{{ $log->description ?? '-' }}</td>
                                <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-clock-history fs-1 text-muted"></i>
                    <p class="text-muted mt-3">No recent activity.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Quick Links</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-people me-2"></i> Manage Users
                    </a>
                    <a href="{{ route('super-admin.sectors.index') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-grid me-2"></i> Manage Sectors
                    </a>
                    <a href="{{ route('admin.business-profiles.index') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-shop me-2"></i> Directory Profiles
                    </a>
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-person-badge me-2"></i> Staff Clearance
                    </a>
                    <a href="{{ route('super-admin.staff-document-types.index') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-person-vcard me-2"></i> Staff Document Types
                    </a>
                    <a href="{{ route('super-admin.reports.index') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-bar-chart me-2"></i> View Reports
                    </a>
                    <a href="{{ route('super-admin.audit-logs.index') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-journal-text me-2"></i> Audit Logs
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    @if(isset($reports['by_sector']) && $reports['by_sector']->isNotEmpty())
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Sector Breakdown</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Sector</th>
                                <th class="text-end">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports['by_sector'] as $row)
                            <tr>
                                <td>{{ $row->sector->name ?? 'Unclassified' }}</td>
                                <td class="text-end">{{ $row->total }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(isset($reports['by_state']) && $reports['by_state']->isNotEmpty())
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">State Breakdown</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>State</th>
                                <th class="text-end">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports['by_state'] as $row)
                            <tr>
                                <td>{{ $row->state ?? 'Unknown' }}</td>
                                <td class="text-end">{{ $row->total }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(isset($reports['by_status']) && $reports['by_status']->isNotEmpty())
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Status Breakdown</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th class="text-end">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports['by_status'] as $row)
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
                                $color = $statusColors[$row->status] ?? 'secondary';
                            @endphp
                            <tr>
                                <td><span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $row->status)) }}</span></td>
                                <td class="text-end">{{ $row->total }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection