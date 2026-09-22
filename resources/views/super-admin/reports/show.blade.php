@extends('layouts.dashboard')

@section('title', 'Report Results')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Report Results</h1>
    <a href="{{ route('super-admin.reports.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> New Report
    </a>
</div>

@if(!empty($filters) && count(array_filter($filters)))
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Applied Filters</h5>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                @if(!empty($filters['sector_id']))
                    <span class="badge bg-primary fs-6">Sector: {{ $sectors->firstWhere('id', $filters['sector_id'])->name ?? $filters['sector_id'] }}</span>
                @endif
                @if(!empty($filters['state']))
                    <span class="badge bg-info fs-6">State: {{ $filters['state'] }}</span>
                @endif
                @if(!empty($filters['status']))
                    <span class="badge bg-warning text-dark fs-6">Status: {{ ucfirst(str_replace('_', ' ', $filters['status'])) }}</span>
                @endif
                @if(!empty($filters['date_from']))
                    <span class="badge bg-secondary fs-6">From: {{ $filters['date_from'] }}</span>
                @endif
                @if(!empty($filters['date_to']))
                    <span class="badge bg-secondary fs-6">To: {{ $filters['date_to'] }}</span>
                @endif
            </div>
        </div>
    </div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md">
        <div class="card text-bg-primary h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase text opacity-75">Total Results</h6>
                <h2 class="mb-0">{{ $businesses->total() }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($businesses->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Results Found</h5>
                <p class="text-muted">No businesses match the selected filters.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Business Name</th>
                            <th>Registration No.</th>
                            <th>Sector</th>
                            <th>State</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $statusColors = [
                                'draft' => 'secondary',
                                'submitted' => 'info',
                                'under_review' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'verified' => 'success',
                                'expired' => 'secondary',
                                'suspended' => 'danger',
                            ];
                        @endphp
                        @foreach($businesses as $business)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $business->business_name }}</span>
                                    @if($business->trading_name)
                                        <br><small class="text-muted">Trading as {{ $business->trading_name }}</small>
                                    @endif
                                </td>
                                <td>{{ $business->registration_number ?? '—' }}</td>
                                <td>{{ $business->sector->name ?? '—' }}</td>
                                <td>{{ $business->state }}</td>
                                <td>
                                    @php $color = $statusColors[$business->status] ?? 'secondary'; @endphp
                                    <span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $business->status)) }}</span>
                                </td>
                                <td>{{ $business->created_at->format('d M Y') }}</td>
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
