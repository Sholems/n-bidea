@extends('layouts.dashboard')

@section('title', "Business Verification - {$business->business_name}")

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Business Verification</h1>
        <small class="text-muted">{{ $business->business_name }}</small>
    </div>
    <a href="{{ route('government.search') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Search
    </a>
</div>

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

@if(in_array($business->status, ['verified', 'approved']))
    <div class="card border-success mb-4">
        <div class="card-body text-center py-4">
            <i class="bi bi-patch-check-fill text-success" style="font-size: 3rem;"></i>
            <h3 class="mt-2 mb-1 text-success">VERIFIED</h3>
            <p class="text-muted mb-2">
                This business is currently verified.
                @if($business->registry_number)
                    Registry No.: <strong>{{ $business->registry_number }}</strong>
                @endif
            </p>
            @if($business->verification_expires_at)
                <p class="mb-0">
                    <small class="text-muted">Verification expires:</small>
                    <strong class="{{ $business->verification_expires_at->isPast() ? 'text-danger' : '' }}">
                        {{ $business->verification_expires_at->format('d M Y') }}
                    </strong>
                    @if($business->verification_expires_at->isPast())
                        <span class="badge bg-danger ms-1">Expired</span>
                    @elseif($business->verification_expires_at->diffInDays(now()) <= 30)
                        <span class="badge bg-warning text-dark ms-1">Expiring Soon</span>
                    @endif
                </p>
            @endif
        </div>
    </div>
@else
    <div class="card border-{{ $color }} mb-4">
        <div class="card-body text-center py-4">
            @if(in_array($business->status, ['rejected', 'suspended']))
                <i class="bi bi-x-circle-fill text-{{ $color }}" style="font-size: 3rem;"></i>
            @else
                <i class="bi bi-hourglass-split text-{{ $color }}" style="font-size: 3rem;"></i>
            @endif
            <h4 class="mt-2 mb-1">
                <span class="badge bg-{{ $color }} fs-5">{{ ucfirst(str_replace('_', ' ', $business->status)) }}</span>
            </h4>
            <p class="text-muted mb-0">Current verification status of this business.</p>
        </div>
    </div>
@endif

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
                        <label class="form-label text-muted small">Trading Name</label>
                        <div class="fw-semibold">{{ $business->trading_name ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Registry Number</label>
                        <div class="fw-semibold">{{ $business->registry_number ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">CAC Number</label>
                        <div class="fw-semibold">{{ $business->cac_number ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">NRS Number</label>
                        <div class="fw-semibold">{{ $business->nrs_number ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Business Type</label>
                        <div class="fw-semibold">{{ $business->business_type ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Sector</label>
                        <div class="fw-semibold">{{ $business->sector->name ?? '—' }}</div>
                    </div>
                    @if($business->description)
                        <div class="col-12">
                            <label class="form-label text-muted small">Description</label>
                            <div>{{ $business->description }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Location</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label text-muted small">Address</label>
                        <div class="fw-semibold">{{ $business->address }}</div>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label text-muted small">State</label>
                        <div class="fw-semibold">{{ $business->state }}</div>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label text-muted small">LGA</label>
                        <div class="fw-semibold">{{ $business->lga }}</div>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label text-muted small">City</label>
                        <div class="fw-semibold">{{ $business->city ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-telephone me-2"></i>Contact Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-4">
                        <label class="form-label text-muted small">Phone</label>
                        <div class="fw-semibold">{{ $business->phone }}</div>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label text-muted small">Email</label>
                        <div class="fw-semibold">{{ $business->email }}</div>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label text-muted small">Website</label>
                        <div class="fw-semibold">
                            @if($business->website)
                                <a href="{{ $business->website }}" target="_blank" rel="noopener noreferrer">{{ $business->website }}</a>
                            @else
                                —
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person me-2"></i>Contact Person</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-4">
                        <label class="form-label text-muted small">Name</label>
                        <div class="fw-semibold">{{ $business->contact_person_name ?? '—' }}</div>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label text-muted small">Phone</label>
                        <div class="fw-semibold">{{ $business->contact_person_phone ?? '—' }}</div>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label text-muted small">Email</label>
                        <div class="fw-semibold">{{ $business->contact_person_email ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Trade Activity</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Trade Activity</label>
                        <div class="fw-semibold">{{ $business->trade_activity ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Border Route</label>
                        <div class="fw-semibold">{{ $business->border_route ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if(in_array(auth()->user()->role, ['super_admin', 'admin']))
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-file-earmark me-2"></i>Documents</h5>
                </div>
                <div class="card-body">
                    <small class="text-muted d-block mb-3">Document access is restricted based on your authorization level.</small>
                    @if($business->documents->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-file-earmark-x fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">No documents uploaded.</p>
                        </div>
                    @else
                        @php
                            $docStatusColors = [
                                'pending' => 'warning',
                                'accepted' => 'success',
                                'rejected' => 'danger',
                                'expired' => 'secondary',
                            ];
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Document Type</th>
                                        <th>Status</th>
                                        <th>Upload Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($business->documents as $document)
                                        <tr>
                                            <td>{{ $document->documentType->name ?? '—' }}</td>
                                            <td>
                                                @php
                                                    $docColor = $docStatusColors[$document->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $docColor }}">{{ ucfirst($document->status) }}</span>
                                            </td>
                                            <td>{{ $document->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Verification History</h5>
            </div>
            <div class="card-body">
                @if($business->verificationReviews->isEmpty())
                    <div class="text-center py-3">
                        <i class="bi bi-clock fs-1 text-muted"></i>
                        <p class="text-muted mt-2 mb-0">No verification history available.</p>
                    </div>
                @else
                    @foreach($business->verificationReviews as $review)
                        <div class="d-flex mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                            <div class="me-3">
                                @if($review->decision === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($review->decision === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ ucfirst(str_replace('_', ' ', $review->decision)) }}</span>
                                @endif
                            </div>
                            <div>
                                <small class="text-muted d-block">{{ $review->created_at->format('d M Y, h:i A') }}</small>
                                <small class="d-block">by {{ $review->admin->name ?? 'System' }}</small>
                                @if($review->note)
                                    <small class="text-muted d-block mt-1">{{ Str::limit($review->note, 80) }}</small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Record Verification Check</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('government.businesses.record-check', $business) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Action</label>
                        <select name="action" class="form-select" required>
                            <option value="">Select action...</option>
                            <option value="verified_in_person">Verified in Person</option>
                            <option value="verified_documents">Verified Documents</option>
                            <option value="spot_check">Spot Check</option>
                            <option value="follow_up_required">Follow-up Required</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Add verification notes...">{{ old('notes') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Record this verification check?');">
                        <i class="bi bi-clipboard-check me-1"></i> Record Check
                    </button>
                </form>
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                        Check will be recorded at: {{ now()->format('d M Y, h:i A') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
