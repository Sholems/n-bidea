@extends('layouts.dashboard')

@section('title', 'Renewal Request')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Renewal Request</h1>
    <a href="{{ route('admin.renewals.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Renewals
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
                        <div class="fw-semibold">{{ $renewalRequest->business->business_name ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Registration Number</label>
                        <div class="fw-semibold">{{ $renewalRequest->business->registration_number ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Owner</label>
                        <div class="fw-semibold">{{ $renewalRequest->business->user->name ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Sector</label>
                        <div class="fw-semibold">{{ $renewalRequest->business->sector->name ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Current Status</label>
                        <div>
                            @php
                                $businessStatusColors = [
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
                                $bizColor = $businessStatusColors[$renewalRequest->business->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $bizColor }}">
                                {{ ucfirst(str_replace('_', ' ', $renewalRequest->business->status)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Renewal Details</h5>
            </div>
            <div class="card-body">
                @php
                    $renewalStatusColors = [
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    ];
                    $rColor = $renewalStatusColors[$renewalRequest->status] ?? 'secondary';
                @endphp
                <div class="mb-3">
                    <label class="form-label text-muted small">Status</label>
                    <div>
                        <span class="badge bg-{{ $rColor }} fs-6">{{ ucfirst($renewalRequest->status) }}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Requested By</label>
                    <div class="fw-semibold">{{ $renewalRequest->requester->name ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Requested Date</label>
                    <div class="fw-semibold">{{ $renewalRequest->created_at->format('d M Y, h:i A') }}</div>
                </div>
                @if($renewalRequest->reason)
                    <div class="mb-3">
                        <label class="form-label text-muted small">Reason for Renewal</label>
                        <div class="bg-light rounded p-2">{{ $renewalRequest->reason }}</div>
                    </div>
                @endif
                <div class="mb-3">
                    <label class="form-label text-muted small">Current Expiry Date</label>
                    <div class="fw-semibold {{ $renewalRequest->previous_expiry_date && $renewalRequest->previous_expiry_date->isPast() ? 'text-danger' : '' }}">
                        {{ $renewalRequest->previous_expiry_date ? $renewalRequest->previous_expiry_date->format('d M Y') : '—' }}
                    </div>
                </div>
                @if($renewalRequest->new_expiry_date)
                    <div class="mb-3">
                        <label class="form-label text-muted small">New Expiry Date</label>
                        <div class="fw-semibold text-success">{{ $renewalRequest->new_expiry_date->format('d M Y') }}</div>
                    </div>
                @endif
                @if($renewalRequest->admin_note)
                    <div class="mb-3">
                        <label class="form-label text-muted small">Admin Note</label>
                        <div>{{ $renewalRequest->admin_note }}</div>
                    </div>
                @endif
            </div>
        </div>

        @if($renewalRequest->status === 'pending')
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Actions</h5>
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <form action="{{ route('admin.renewals.approve', $renewalRequest) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Approve this renewal request?');">
                            <i class="bi bi-check-lg"></i> Approve Renewal
                        </button>
                    </form>
                    <form action="{{ route('admin.renewals.reject', $renewalRequest) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Reject this renewal request?');">
                            <i class="bi bi-x-lg"></i> Reject Renewal
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
