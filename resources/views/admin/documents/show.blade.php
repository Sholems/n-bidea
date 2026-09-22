@extends('layouts.dashboard')

@section('title', 'Document Review')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Document Review</h1>
    <a href="{{ route('admin.businesses.show', $document->business) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Business
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Document Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Document Type</label>
                        <div class="fw-semibold">{{ $document->documentType->name ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Business</label>
                        <div class="fw-semibold">{{ $document->business->business_name ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">File Name</label>
                        <div class="fw-semibold">
                            <i class="bi bi-file-earmark me-1"></i>
                            {{ $document->original_file_name }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">File Size</label>
                        <div class="fw-semibold">
                            @if($document->file_size)
                                {{ number_format($document->file_size / 1024, 1) }} KB
                            @else
                                —
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">MIME Type</label>
                        <div class="fw-semibold">{{ $document->mime_type ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Uploaded</label>
                        <div class="fw-semibold">{{ $document->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Status</label>
                        <div>
                            @php
                                $docStatusColors = [
                                    'pending' => 'warning',
                                    'accepted' => 'success',
                                    'rejected' => 'danger',
                                    'expired' => 'secondary',
                                ];
                                $docColor = $docStatusColors[$document->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $docColor }} fs-6">{{ ucfirst($document->status) }}</span>
                        </div>
                    </div>
                    @if($document->reviewed_by)
                        <div class="col-sm-6">
                            <label class="form-label text-muted small">Reviewed By</label>
                            <div class="fw-semibold">{{ $document->reviewer->name ?? '—' }}</div>
                        </div>
                    @endif
                    @if($document->reviewed_at)
                        <div class="col-sm-6">
                            <label class="form-label text-muted small">Reviewed At</label>
                            <div class="fw-semibold">{{ $document->reviewed_at->format('d M Y, h:i A') }}</div>
                        </div>
                    @endif
                    @if($document->admin_note)
                        <div class="col-12">
                            <label class="form-label text-muted small">Admin Note</label>
                            <div>{{ $document->admin_note }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-download me-2"></i>Download</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.documents.download', $document) }}" class="btn btn-outline-primary">
                    <i class="bi bi-download me-1"></i> Download {{ $document->original_file_name }}
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Review Document</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.documents.review', $document) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold">Decision</label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="accepted" {{ old('status', $document->status) === 'accepted' ? 'selected' : '' }}>Accept</option>
                            <option value="rejected" {{ old('status', $document->status) === 'rejected' ? 'selected' : '' }}>Reject</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="admin_note" class="form-label fw-semibold">Note</label>
                        <textarea name="admin_note" id="admin_note" class="form-control @error('admin_note') is-invalid @enderror" rows="4" placeholder="Add review notes...">{{ old('admin_note', $document->admin_note) }}</textarea>
                        @error('admin_note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Submit document review?');">
                        <i class="bi bi-check-lg"></i> Submit Review
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
