@extends('layouts.dashboard')

@section('title', "Review Application - {$business->business_name}")

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Review Application</h1>
        <small class="text-muted">{{ $business->business_name }}</small>
    </div>
    <a href="{{ route('admin.businesses.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Applications
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
                        <label class="form-label text-muted small">Registration Number</label>
                        <div class="fw-semibold">{{ $business->registration_number ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Business Type</label>
                        <div class="fw-semibold">{{ $business->business_type ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Sector</label>
                        <div class="fw-semibold">{{ $business->sector->name ?? '—' }}</div>
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
                        <label class="form-label text-muted small">NIN</label>
                        <div class="fw-semibold">{{ $business->nin ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Trade Activity</label>
                        <div class="fw-semibold">{{ $business->trade_activity ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Border Route</label>
                        <div class="fw-semibold">{{ $business->border_route ?? '—' }}</div>
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
                <h5 class="mb-0"><i class="bi bi-person me-2"></i>Owner Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-4">
                        <label class="form-label text-muted small">Name</label>
                        <div class="fw-semibold">{{ $business->user->name ?? '—' }}</div>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label text-muted small">Email</label>
                        <div class="fw-semibold">{{ $business->user->email ?? '—' }}</div>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label text-muted small">Phone</label>
                        <div class="fw-semibold">{{ $business->user->phone ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-file-earmark me-2"></i>Documents</h5>
            </div>
            <div class="card-body p-0">
                @if($documents->isEmpty())
                    <div class="text-center py-4">
                        <i class="bi bi-file-earmark-x fs-1 text-muted"></i>
                        <p class="text-muted mt-2 mb-0">No documents uploaded yet.</p>
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
                                    <th>Type</th>
                                    <th>File</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $document)
                                    <tr>
                                        <td>{{ $document->documentType->name ?? '—' }}</td>
                                        <td>
                                            <i class="bi bi-file-earmark me-1"></i>
                                            {{ $document->original_file_name }}
                                        </td>
                                        <td>
                                            @php
                                                $docColor = $docStatusColors[$document->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $docColor }}">{{ ucfirst($document->status) }}</span>
                                        </td>
                                        <td>
                                            @if($document->admin_note)
                                                <small class="text-muted">{{ Str::limit($document->admin_note, 40) }}</small>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.documents.download', $document) }}" class="btn btn-outline-primary" title="Download">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <a href="{{ route('admin.documents.show', $document) }}" class="btn btn-outline-secondary" title="Review">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Status</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
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
                        <small class="text-muted">Verification Expiry: <span class="fw-semibold {{ $business->verification_expires_at->isPast() ? 'text-danger' : '' }}">{{ $business->verification_expires_at->format('d M Y') }}</span></small>
                    </div>
                @endif
            </div>
        </div>

        @if($business->status === 'approved')
            <div class="card mb-4 border-warning">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-patch-check me-2"></i>Business Verification</h5>
                </div>
                <div class="card-body">
                    @if(auth()->user()->role === 'super_admin')
                        <p class="text-muted small">Record the independent check completed before granting the public verification mark.</p>
                        <form action="{{ route('super-admin.businesses.verification.store', $business) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="method" class="form-label fw-semibold">Verification Method</label>
                                <select name="method" id="method" class="form-select @error('method') is-invalid @enderror" required>
                                    <option value="">Select method...</option>
                                    <option value="phone_call" @selected(old('method') === 'phone_call')>Phone call</option>
                                    <option value="site_visit" @selected(old('method') === 'site_visit')>Site visit</option>
                                </select>
                                @error('method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Decision</label>
                                <div class="d-flex flex-column gap-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="decision" id="verification_verified" value="verified" @checked(old('decision') === 'verified') required>
                                        <label class="form-check-label" for="verification_verified">Grant verified status</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="decision" id="verification_not_verified" value="not_verified" @checked(old('decision') === 'not_verified')>
                                        <label class="form-check-label" for="verification_not_verified">Do not verify</label>
                                    </div>
                                </div>
                                @error('decision')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label for="verification_note" class="form-label fw-semibold">Call or Visit Notes</label>
                                <textarea name="note" id="verification_note" class="form-control @error('note') is-invalid @enderror" rows="4" required>{{ old('note') }}</textarea>
                                @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check2-circle me-1"></i> Record Verification Decision
                            </button>
                        </form>
                    @else
                        <p class="mb-0 text-muted">Registration is approved. A Super Admin must complete a phone call or site visit before the verification mark can be granted.</p>
                    @endif
                </div>
            </div>
        @elseif($business->status === 'verified')
            <div class="card mb-4 border-success">
                <div class="card-body">
                    <div class="d-flex gap-3 align-items-center">
                        <i class="bi bi-patch-check-fill text-success fs-2"></i>
                        <div>
                            <div class="fw-semibold">Verified by NB-CCI</div>
                            <div class="text-muted small">Valid until {{ $business->verification_expires_at?->format('d M Y') ?? 'not specified' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(in_array($business->status, ['draft', 'submitted', 'under_review', 'correction_required'], true))
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Review Decision</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.businesses.review', $business) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Decision</label>
                        <div class="d-flex flex-column gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="decision" id="decision_approved" value="approved" {{ old('decision') === 'approved' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="decision_approved">
                                    <span class="badge bg-success me-1">Approve</span> Approve this application
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="decision" id="decision_rejected" value="rejected" {{ old('decision') === 'rejected' ? 'checked' : '' }}>
                                <label class="form-check-label" for="decision_rejected">
                                    <span class="badge bg-danger me-1">Reject</span> Reject this application
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="decision" id="decision_correction_required" value="correction_required" {{ old('decision') === 'correction_required' ? 'checked' : '' }}>
                                <label class="form-check-label" for="decision_correction_required">
                                    <span class="badge bg-warning text-dark me-1">Correction</span> Request corrections
                                </label>
                            </div>
                        </div>
                        @error('decision')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label fw-semibold">Note</label>
                        <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror" rows="4" placeholder="Add review notes...">{{ old('note') }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Submit this review decision?');">
                        <i class="bi bi-check-lg"></i> Submit Review
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($verificationReviews->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Review History</h5>
                </div>
                <div class="card-body">
                    @foreach($verificationReviews as $review)
                        <div class="d-flex mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                            <div class="me-3">
                                @if($review->decision === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($review->decision === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-warning text-dark">Correction</span>
                                @endif
                            </div>
                            <div>
                                <small class="text-muted d-block">{{ $review->created_at->format('d M Y, h:i A') }}</small>
                                <small class="d-block">by {{ $review->admin->name ?? 'Admin' }}</small>
                                @if($review->note)
                                    <small class="text-muted d-block mt-1">{{ Str::limit($review->note, 80) }}</small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($business->verificationChecks->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-telephone-check me-2"></i>Verification Check History</h5>
                </div>
                <div class="card-body">
                    @foreach($business->verificationChecks->sortByDesc('checked_at') as $check)
                        <div class="mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                            <div class="d-flex justify-content-between gap-2 mb-1">
                                <span class="badge {{ $check->decision === 'verified' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $check->decision === 'verified' ? 'Verified' : 'Not verified' }}
                                </span>
                                <small class="text-muted">{{ $check->checked_at->format('d M Y, h:i A') }}</small>
                            </div>
                            <div class="small fw-semibold">{{ $check->method === 'site_visit' ? 'Site visit' : 'Phone call' }} by {{ $check->superAdmin->name ?? 'Super Admin' }}</div>
                            <div class="small text-muted mt-1">{{ $check->note }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($fees->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Fees</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fees as $fee)
                                    <tr>
                                        <td>{{ ucfirst(str_replace('_', ' ', $fee->fee_type)) }}</td>
                                        <td>₦{{ number_format($fee->amount, 2) }}</td>
                                        <td>
                                            @if($fee->payment_status === 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
