@extends('layouts.dashboard')

@section('title', 'Business Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Business Details</h1>
    <div class="d-flex gap-2">
        @can('update', $business)
            <a href="{{ route('business-owner.businesses.edit', $business) }}" class="btn btn-outline-secondary">
                <i class="bi bi-pencil"></i> Edit
            </a>
        @endcan
        @if(in_array($business->status, ['approved', 'verified']))
            <a href="{{ route('business-owner.businesses.profile.show', $business) }}" class="btn btn-outline-success">
                <i class="bi bi-shop"></i> Public Listing
            </a>
            <a href="{{ route('business-owner.businesses.staff.index', $business) }}" class="btn btn-outline-primary">
                <i class="bi bi-person-vcard"></i> Staff &amp; Documents
            </a>
        @endif
        @if($business->status === 'draft')
            <form action="{{ route('business-owner.businesses.submit', $business) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send"></i> Submit for Review
                </button>
            </form>
        @endif
        <a href="{{ route('business-owner.businesses.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>
</div>

@if(in_array($business->status, ['approved', 'verified']))
    <div class="alert alert-light border d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <strong class="d-block">Public visibility and staff records</strong>
            <span class="text-muted">Your approved business is listed automatically. Use Public Listing to enrich what partners see, or Staff &amp; Documents to add personnel and upload their required files.</span>
        </div>
        <a href="{{ route('business-owner.businesses.staff.index', $business) }}" class="btn btn-primary flex-shrink-0"><i class="bi bi-upload me-1"></i> Upload Staff Documents</a>
    </div>
@endif

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
@endphp

@if($business->status === 'correction_required')
    <div class="card border-warning mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Corrections Required</h5>
        </div>
        <div class="card-body">
            <p class="mb-3">Please review the admin feedback, update your application details, and submit your response below.</p>
            <form action="{{ route('business-owner.businesses.correction-response', $business) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="note" class="form-label fw-semibold">Your Response</label>
                    <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror" rows="4" placeholder="Describe the corrections you have made...">{{ old('note') }}</textarea>
                    @error('note')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-send me-1"></i> Submit Correction Response
                </button>
            </form>
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
                @if($business->profile?->logo_path)
                    <div class="mb-3">
                        <img src="{{ route('business-owner.businesses.profile.logo', $business) }}" alt="{{ $business->business_name }} logo" class="border rounded" style="width: 96px; height: 96px; object-fit: contain; background: #fff;">
                    </div>
                @endif
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
                        <label class="form-label text-muted small">Country of Registration</label>
                        <div class="fw-semibold">{{ \App\Models\Business::COUNTRIES[$business->country_code] ?? $business->country_code }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Sector</label>
                        <div class="fw-semibold">{{ $business->sector->name ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Registry Number</label>
                        <div class="fw-semibold">{{ $business->registry_number ?? '—' }}</div>
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
                    <div class="col-12"><hr></div>
                    <div class="col-12">
                        <h6 class="text-muted mb-3">Contact Person</h6>
                    </div>
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
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Status</h5>
            </div>
            <div class="card-body">
                @php
                    $color = $statusColors[$business->status] ?? 'secondary';
                    $extraClass = $business->status === 'correction_required' ? ' text-dark' : '';
                @endphp
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-{{ $color }}{{ $extraClass }} fs-6 me-3">
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

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Registration Details</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted d-block">CAC Number</small>
                    <span class="fw-semibold">{{ $business->cac_number ?? '—' }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">NRS Number</small>
                    <span class="fw-semibold">{{ $business->nrs_number ?? '—' }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">NIN</small>
                    <span class="fw-semibold">{{ $business->nin ?? '—' }}</span>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-globe me-2"></i>Trade Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted d-block">Trade Activity</small>
                    <span class="fw-semibold">{{ $business->trade_activity ?? '—' }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Border Route</small>
                    <span class="fw-semibold">{{ $business->border_route ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-file-earmark me-2"></i>Documents</h5>
        <a href="{{ route('business-owner.businesses.documents.index', $business) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-folder2-open"></i> Manage Documents
        </a>
    </div>
    @if(in_array($business->status, ['draft', 'correction_required']))
        <div class="card-body border-bottom">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-2">
                <div>
                    <strong>Document readiness</strong>
                    <div class="text-muted small">{{ $requirementSummary['uploaded_required_count'] }} of {{ $requirementSummary['required_count'] }} required documents uploaded</div>
                </div>
                <span class="fw-semibold text-primary">{{ $requirementSummary['completion_percentage'] }}%</span>
            </div>
            <div class="progress" role="progressbar" aria-label="Required document completion" aria-valuenow="{{ $requirementSummary['completion_percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="height: 0.5rem;">
                <div class="progress-bar" style="width: {{ $requirementSummary['completion_percentage'] }}%"></div>
            </div>
        </div>
    @endif
    <div class="card-body p-0">
        @if($documents->isEmpty())
            <div class="text-center py-4">
                <i class="bi bi-file-earmark-x fs-1 text-muted"></i>
                <p class="text-muted mt-2 mb-0">No documents uploaded yet.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Document Type</th>
                            <th>File Name</th>
                            <th>Uploaded</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $document)
                            <tr>
                                <td>{{ $document->documentType->name ?? 'Document' }}</td>
                                <td>{{ $document->original_file_name }}</td>
                                <td>{{ $document->created_at->format('d M Y') }}</td>
                                <td>
                                    @php
                                        $docStatusColors = [
                                            'pending' => 'secondary',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                        ];
                                        $docColor = $docStatusColors[$document->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $docColor }}">{{ ucfirst($document->status) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
