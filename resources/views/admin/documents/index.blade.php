@extends('layouts.dashboard')

@section('title', "Documents - {$business->business_name}")

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Documents</h1>
        <small class="text-muted">{{ $business->business_name }}</small>
    </div>
    <a href="{{ route('admin.businesses.show', $business) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Business
    </a>
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
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Uploaded Documents</h5>
        <span class="badge bg-secondary">{{ $documents->total() }}</span>
    </div>
    <div class="card-body p-0">
        @if($documents->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-file-earmark-x fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Documents</h5>
                <p class="text-muted mb-0">This business has not uploaded any documents yet.</p>
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
                                        $color = $statusColors[$document->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ ucfirst($document->status) }}</span>
                                </td>
                                <td>{{ $document->created_at->format('d M Y') }}</td>
                                <td>{{ $document->reviewer->name ?? '—' }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.documents.show', $document) }}" class="btn btn-outline-primary" title="Review">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.documents.download', $document) }}" class="btn btn-outline-secondary" title="Download">
                                            <i class="bi bi-download"></i>
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
    @if($documents->hasPages())
        <div class="card-footer bg-white">
            {{ $documents->links() }}
        </div>
    @endif
</div>
@endsection
