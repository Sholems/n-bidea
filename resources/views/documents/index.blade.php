@extends('layouts.dashboard')

@section('title', "Documents - {$business->business_name}")

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Documents</h1>
        <small class="text-muted">{{ $business->business_name }}</small>
    </div>
    <a href="{{ route('business-owner.businesses.show', $business) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Business
    </a>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-upload me-2"></i>Upload Document</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('business-owner.businesses.documents.store', $business) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="document_type_id" class="form-label">Document Type</label>
                    <select name="document_type_id" id="document_type_id" class="form-select @error('document_type_id') is-invalid @enderror" required>
                        <option value="">Select document type...</option>
                        @foreach(\App\Models\DocumentType::where('status', 'active')->get() as $type)
                            <option value="{{ $type->id }}" {{ old('document_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} {{ $type->is_required ? '(Required)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('document_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="file" class="form-label">File</label>
                    <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required>
                    @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Accepted formats: PDF, JPG, JPEG, PNG</small>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-upload"></i> Upload
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@php
    $statusColors = [
        'pending' => 'warning',
        'accepted' => 'success',
        'rejected' => 'danger',
        'expired' => 'secondary',
    ];
@endphp

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Uploaded Documents</h5>
    </div>
    <div class="card-body p-0">
        @if($business->documents->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-file-earmark-x fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Documents Yet</h5>
                <p class="text-muted mb-0">Upload your first document using the form above.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>File Name</th>
                            <th>Status</th>
                            <th>Uploaded</th>
                            <th>Reviewed By</th>
                            <th>Notes</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($business->documents as $document)
                            <tr>
                                <td>{{ $document->documentType->name ?? '—' }}</td>
                                <td>
                                    <i class="bi bi-file-earmark me-1"></i>
                                    {{ $document->original_file_name }}
                                </td>
                                <td>
                                    @php
                                        $color = $statusColors[$document->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ ucfirst($document->status) }}</span>
                                </td>
                                <td>{{ $document->created_at->format('d M Y') }}</td>
                                <td>{{ $document->reviewer->name ?? '—' }}</td>
                                <td>
                                    @if($document->admin_note)
                                        <small class="text-muted">{{ Str::limit($document->admin_note, 50) }}</small>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        @if(file_exists(storage_path('app/private/'.$document->file_path)))
                                            <a href="{{ route('business-owner.documents.download', $document) }}" class="btn btn-outline-primary" title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif
                                        @if(in_array($document->status, ['rejected', 'expired']))
                                            <form action="{{ route('business-owner.documents.replace', $document) }}" method="POST" class="d-inline" enctype="multipart/form-data">
                                                @csrf
                                                <label class="btn btn-outline-warning btn-sm mb-0" title="Replace" style="cursor:pointer;">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                    <input type="file" name="file" class="d-none" accept=".pdf,.jpg,.jpeg,.png" onchange="this.form.submit()">
                                                </label>
                                            </form>
                                        @endif
                                        @if($document->status === 'pending')
                                            <form action="{{ route('business-owner.documents.destroy', $document) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
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
@endsection
