@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')
<h1 class="h3 mb-4">Admin Dashboard</h1>

<div class="row g-3 mb-4">
    <div class="col-md">
        <div class="card text-bg-primary h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Total Applications</h6>
                <h2 class="mb-0">{{ $counts['total'] }}</h2>
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
                <h6 class="card-title text-uppercase opacity-75">Under Review</h6>
                <h2 class="mb-0">{{ $counts['under_review'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card text-bg-danger h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Correction Required</h6>
                <h2 class="mb-0">{{ $counts['correction_required'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card text-bg-success h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Approved</h6>
                <h2 class="mb-0">{{ $counts['approved'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md">
        <div class="card text-bg-danger h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Rejected</h6>
                <h2 class="mb-0">{{ $counts['rejected'] }}</h2>
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
        <div class="card text-bg-success h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Verified</h6>
                <h2 class="mb-0">{{ $counts['verified'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Submissions</h5>
            </div>
            <div class="card-body p-0">
                @if($recentSubmissions->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Business Name</th>
                                <th>Submitted By</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentSubmissions as $submission)
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
                                $color = $statusColors[$submission->status] ?? 'secondary';
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('admin.businesses.show', $submission) }}">
                                        {{ $submission->business_name }}
                                    </a>
                                </td>
                                <td>{{ $submission->user->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $submission->status)) }}</span></td>
                                <td>{{ $submission->created_at->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-3">No recent submissions.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Pending Reviews</h5>
                <span class="badge bg-secondary">{{ $pendingReviews->total() }}</span>
            </div>
            <div class="card-body">
                @if($pendingReviews->isNotEmpty())
                <ul class="list-group list-group-flush">
                    @foreach($pendingReviews as $review)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <a href="{{ route('admin.businesses.show', $review) }}" class="text-decoration-none">
                                {{ $review->business_name }}
                            </a>
                            <br>
                            <small class="text-muted">{{ $review->created_at->format('d M Y') }}</small>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="text-center py-3">
                    <i class="bi bi-check-circle fs-1 text-success"></i>
                    <p class="text-muted mt-2 mb-0">All caught up! No pending reviews.</p>
                </div>
                @endif
            </div>
            @if($pendingReviews->hasPages())
                <div class="card-footer bg-white">
                    {{ $pendingReviews->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection