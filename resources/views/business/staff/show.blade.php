@extends('layouts.dashboard')

@section('title', $staffMember->full_name)

@section('content')
@php
    $statusColors = [
        'draft' => 'secondary',
        'submitted' => 'info',
        'correction_required' => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
        'expired' => 'secondary',
    ];
    $docColors = ['pending' => 'warning', 'accepted' => 'success', 'rejected' => 'danger'];
    $canManage = auth()->user()->can('manageDocuments', $staffMember);
    $uploadedTypeIds = $staffMember->documents->pluck('staff_document_type_id');
@endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-0">{{ $staffMember->full_name }}</h1>
        <small class="text-muted">{{ $staffMember->job_title }} &middot; {{ $staffMember->business->business_name }}</small>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('business-owner.businesses.staff.index', $staffMember->business) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> All Staff
        </a>
        @can('update', $staffMember)
            <a href="{{ route('business-owner.staff.edit', $staffMember) }}" class="btn btn-outline-secondary">
                <i class="bi bi-pencil"></i> Edit
            </a>
        @endcan
        @if($staffMember->status === 'draft')
            <form action="{{ route('business-owner.staff.submit', $staffMember) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success"><i class="bi bi-send"></i> Submit for Review</button>
            </form>
        @endif
        @can('delete', $staffMember)
            <form action="{{ route('business-owner.staff.destroy', $staffMember) }}" method="POST" onsubmit="return confirm('Remove this staff member and their uploaded documents?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i> Remove</button>
            </form>
        @endcan
    </div>
</div>

@if($staffMember->status === 'correction_required')
    <div class="card border-warning mb-4">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-exclamation-triangle text-warning"></i> Correction Required</h5>
            <p class="mb-3">{{ $staffMember->review_note ?? 'The reviewer asked for changes.' }}</p>
            <form action="{{ route('business-owner.staff.correction-response', $staffMember) }}" method="POST">
                @csrf
                <label for="note" class="form-label">Your response</label>
                <textarea name="note" id="note" rows="3" class="form-control @error('note') is-invalid @enderror" required>{{ old('note') }}</textarea>
                @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <button type="submit" class="btn btn-warning mt-3">Submit Response</button>
            </form>
        </div>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Details</div>
            <div class="card-body">
                <p class="mb-2">
                    <span class="badge bg-{{ $statusColors[$staffMember->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $staffMember->status)) }}</span>
                </p>
                <dl class="mb-0 small">
                    <dt>Staff Number</dt><dd>{{ $staffMember->staff_number ?? 'Assigned on approval' }}</dd>
                    <dt>Phone</dt><dd>{{ $staffMember->phone }}</dd>
                    <dt>Email</dt><dd>{{ $staffMember->email ?? '—' }}</dd>
                    <dt>Nationality</dt><dd>{{ $staffMember->nationality }}</dd>
                    <dt>Date of Birth</dt><dd>{{ $staffMember->date_of_birth?->format('d M Y') ?? '—' }}</dd>
                    <dt>NIN</dt><dd>{{ $staffMember->nin ?? '—' }}</dd>
                    <dt>Passport / Travel Document</dt><dd>{{ $staffMember->passport_number ?? '—' }}</dd>
                    <dt>Valid Until</dt><dd class="mb-0">{{ $staffMember->verification_expires_at?->format('d M Y') ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Documents</div>
            <div class="card-body p-0">
                @if($staffMember->documents->isEmpty())
                    <p class="text-muted text-center py-4 mb-0">No documents uploaded yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr><th>Type</th><th>File</th><th>Status</th><th class="text-end">Actions</th></tr>
                            </thead>
                            <tbody>
                                @foreach($staffMember->documents as $document)
                                    <tr>
                                        <td>{{ $document->documentType->name }}</td>
                                        <td>
                                            <a href="{{ route('business-owner.staff-documents.download', $document) }}">{{ $document->original_file_name }}</a>
                                            @if($document->admin_note)
                                                <br><small class="text-danger">{{ $document->admin_note }}</small>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-{{ $docColors[$document->status] ?? 'secondary' }}">{{ ucfirst($document->status) }}</span></td>
                                        <td class="text-end">
                                            @if($canManage && $document->status !== 'accepted')
                                                <form action="{{ route('business-owner.staff-documents.replace', $document) }}" method="POST" enctype="multipart/form-data" class="d-inline-flex gap-1">
                                                    @csrf
                                                    <input type="file" name="file" class="form-control form-control-sm" required>
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">Replace</button>
                                                </form>
                                                <form action="{{ route('business-owner.staff-documents.destroy', $document) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this document?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        @if($canManage)
            <div class="card border-primary">
                <div class="card-header bg-primary text-white fw-semibold"><i class="bi bi-upload me-2"></i>Upload Staff Document</div>
                <div class="card-body">
                    <p class="text-muted small">Choose the correct document type and upload a clear PDF, JPG, or PNG file. Required documents must be present before submission.</p>
                    <form action="{{ route('business-owner.staff.documents.store', $staffMember) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label for="staff_document_type_id" class="form-label">Document Type</label>
                                <select name="staff_document_type_id" id="staff_document_type_id" class="form-select @error('staff_document_type_id') is-invalid @enderror" required>
                                    <option value="">Select type</option>
                                    @foreach($documentTypes as $type)
                                        <option value="{{ $type->id }}" @selected(old('staff_document_type_id') == $type->id)>
                                            {{ $type->name }}@if($type->is_required) (required)@endif @if($uploadedTypeIds->contains($type->id)) &#10003; @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('staff_document_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-5">
                                <label for="file" class="form-label">File (PDF, JPG, PNG, max 5MB)</label>
                                <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" required>
                                @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-upload"></i> Upload</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
