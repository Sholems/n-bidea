@extends('layouts.dashboard')

@section('title', 'Verification Result')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Verification Result</h1>
    <a href="{{ route('business-owner.businesses.show', $business) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Business
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-1">Business Name</h6>
                <h4 class="mb-0">{{ $business->business_name }}</h4>
                @if($business->registration_number)
                    <small class="text-muted">Reg. No.: {{ $business->registration_number }}</small>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        @php
            $statusColors = [
                'approved' => 'success',
                'rejected' => 'danger',
                'verified' => 'success',
                'expired' => 'secondary',
                'under_review' => 'warning',
                'submitted' => 'info',
                'draft' => 'secondary',
                'suspended' => 'danger',
            ];
            $color = $statusColors[$business->status] ?? 'secondary';
            $extraClass = $business->status === 'correction_required' ? ' text-dark' : '';
        @endphp
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <h6 class="text-muted text-uppercase small mb-1">Verification Status</h6>
                <div class="d-flex align-items-center gap-3">
                    @if(in_array($business->status, ['approved', 'verified']))
                        <i class="bi bi-check-circle-fill text-success fs-2"></i>
                    @elseif(in_array($business->status, ['rejected', 'suspended']))
                        <i class="bi bi-x-circle-fill text-danger fs-2"></i>
                    @else
                        <i class="bi bi-hourglass-split text-warning fs-2"></i>
                    @endif
                    <div>
                        <span class="badge bg-{{ $color }}{{ $extraClass }} fs-6">
                            {{ ucfirst(str_replace('_', ' ', $business->status)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($business->verification_expires_at)
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="text-muted text-uppercase small mb-1">Verification Expiry Date</h6>
                <h5 class="mb-0 {{ $business->verification_expires_at->isPast() ? 'text-danger' : '' }}">
                    {{ $business->verification_expires_at->format('d M Y') }}
                    @if($business->verification_expires_at->isPast())
                        <span class="badge bg-danger ms-2">Expired</span>
                    @elseif($business->verification_expires_at->diffInDays(now()) <= 30)
                        <span class="badge bg-warning text-dark ms-2">Expiring Soon</span>
                    @endif
                </h5>
            </div>
            @if(in_array($business->status, ['verified', 'approved']) && $business->verification_expires_at->diffInDays(now()) <= 30)
                <a href="{{ route('business-owner.renewals.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-repeat"></i> Renew Verification
                </a>
            @endif
        </div>
    </div>
</div>
@endif

@if($business->certificate)
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-patch-check me-2"></i>Business Certificate</h5>
    </div>
    <div class="card-body">
        <div class="row g-4 align-items-center">
            <div class="col-md-4">
                <h6 class="text-muted text-uppercase small mb-1">Certificate Number</h6>
                <div class="fw-semibold">{{ $business->certificate->certificate_number }}</div>
            </div>
            <div class="col-md-4">
                <h6 class="text-muted text-uppercase small mb-1">Public Verification</h6>
                <a href="{{ $business->certificate->qr_payload }}" target="_blank" rel="noopener">
                    Open verification page
                </a>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('business-owner.certificates.show', $business->certificate) }}" class="btn btn-primary">
                    <i class="bi bi-file-earmark-check me-2"></i>View Certificate
                </a>
            </div>
        </div>
    </div>
</div>
@endif

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Verification History</h5>
    </div>
    <div class="card-body p-0">
        @if($verificationReviews->isEmpty())
            <div class="text-center py-4">
                <i class="bi bi-clock fs-1 text-muted"></i>
                <p class="text-muted mt-2 mb-0">No verification history available.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reviewer</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $reviewStatusColors = [
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'correction_required' => 'warning',
                                'pending' => 'secondary',
                            ];
                        @endphp
                        @foreach($verificationReviews as $review)
                            <tr>
                                <td>{{ $review->created_at->format('d M Y, h:i A') }}</td>
                                <td>{{ $review->admin->name ?? 'System' }}</td>
                                <td>
                                    @php
                                        $rColor = $reviewStatusColors[$review->decision] ?? 'secondary';
                                        $rExtra = $review->decision === 'correction_required' ? ' text-dark' : '';
                                    @endphp
                                    <span class="badge bg-{{ $rColor }}{{ $rExtra }}">
                                        {{ ucfirst(str_replace('_', ' ', $review->decision)) }}
                                    </span>
                                </td>
                                <td>{{ $review->note ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@if($fees->isNotEmpty())
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Fees & Payments</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Fee Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $feeStatusColors = [
                            'paid' => 'success',
                            'pending' => 'warning',
                            'pending_confirmation' => 'warning',
                            'proof_uploaded' => 'info',
                            'overdue' => 'danger',
                            'waived' => 'info',
                        ];
                    @endphp
                    @foreach($fees as $fee)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $fee->fee_type)) }}</td>
                            <td class="fw-semibold">₦{{ number_format($fee->amount, 2) }}</td>
                            <td>
                                @php
                                    $fColor = $feeStatusColors[$fee->payment_status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $fColor }}">{{ ucfirst(str_replace('_', ' ', $fee->payment_status)) }}</span>
                            </td>
                            <td>{{ $fee->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
