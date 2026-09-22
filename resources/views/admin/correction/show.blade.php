@extends('layouts.dashboard')

@section('title', 'Correction Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Correction Details</h1>
        <small class="text-muted">{{ $business->business_name }}</small>
    </div>
    <a href="{{ route('admin.businesses.show', $business) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Application
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-building me-2"></i>Business Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Business Name</label>
                        <div class="fw-semibold">{{ $business->business_name }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Registration Number</label>
                        <div class="fw-semibold">{{ $business->registration_number ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Owner</label>
                        <div class="fw-semibold">{{ $business->user->name ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Sector</label>
                        <div class="fw-semibold">{{ $business->sector->name ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">State</label>
                        <div class="fw-semibold">{{ $business->state ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Status</label>
                        <div>
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
                                $color = $statusColors[$business->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }}">
                                {{ ucfirst(str_replace('_', ' ', $business->status)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Correction Requests</h5>
            </div>
            <div class="card-body">
                @if($corrections->isEmpty())
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle fs-1 text-success"></i>
                        <h5 class="text-muted mt-3">No Corrections Pending</h5>
                        <p class="text-muted mb-0">There are no correction requests for this business.</p>
                    </div>
                @else
                    @foreach($corrections as $correction)
                        <div class="card {{ !$loop->last ? 'mb-3' : '' }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-warning text-dark">Correction Required</span>
                                        <span class="text-muted small ms-2">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $correction->created_at->format('d M Y, h:i A') }}
                                        </span>
                                    </div>
                                    @if($correction->expires_at)
                                        <small class="text-muted">
                                            Deadline: {{ $correction->expires_at->format('d M Y') }}
                                        </small>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <label class="form-label text-muted small">Note from Admin</label>
                                    <div class="bg-light rounded p-3">
                                        {{ $correction->note ?? 'No specific notes provided.' }}
                                    </div>
                                </div>
                                @if($business->correction_response)
                                    <div class="mt-2">
                                        <label class="form-label text-muted small">Owner Response</label>
                                        <div class="bg-success bg-opacity-10 rounded p-3">
                                            {{ $business->correction_response }}
                                        </div>
                                    </div>
                                @endif
                                <div class="mt-2">
                                    <small class="text-muted">
                                        Reviewer: {{ $correction->admin->name ?? 'Admin' }}
                                        @if($correction->previous_status)
                                            | Previous Status: {{ ucfirst(str_replace('_', ' ', $correction->previous_status)) }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Current Status</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge bg-{{ $color }} fs-6">
                        {{ ucfirst(str_replace('_', ' ', $business->status)) }}
                    </span>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Created: {{ $business->created_at->format('d M Y, h:i A') }}</small>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Last Updated: {{ $business->updated_at->format('d M Y, h:i A') }}</small>
                </div>
                @if($business->verification_expires_at)
                    <div class="mb-2">
                        <small class="text-muted">Verification Expiry:</small>
                        <span class="fw-semibold {{ $business->verification_expires_at->isPast() ? 'text-danger' : '' }}">
                            {{ $business->verification_expires_at->format('d M Y') }}
                        </span>
                    </div>
                @endif
                <hr>
                <div class="mb-2">
                    <small class="text-muted d-block">Total Correction Requests</small>
                    <span class="fw-semibold">{{ $corrections->count() }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Total Documents</small>
                    <span class="fw-semibold">{{ $business->documents->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
