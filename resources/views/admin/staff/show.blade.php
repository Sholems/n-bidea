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
@endphp
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">{{ $staffMember->full_name }}</h1>
        <small class="text-muted">{{ $staffMember->job_title }} &middot;
            <a href="{{ route('admin.businesses.show', $staffMember->business) }}">{{ $staffMember->business->business_name }}</a>
        </small>
    </div>
    <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Staff</a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Details</div>
            <div class="card-body">
                <p class="mb-2"><span class="badge bg-{{ $statusColors[$staffMember->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $staffMember->status)) }}</span></p>
                <dl class="mb-0 small">
                    <dt>Staff Number</dt><dd>{{ $staffMember->staff_number ?? '—' }}</dd>
                    <dt>Phone</dt><dd>{{ $staffMember->phone }}</dd>
                    <dt>Email</dt><dd>{{ $staffMember->email ?? '—' }}</dd>
                    <dt>Nationality</dt><dd>{{ $staffMember->nationality }}</dd>
                    <dt>Date of Birth</dt><dd>{{ $staffMember->date_of_birth?->format('d M Y') ?? '—' }}</dd>
                    <dt>NIN</dt><dd>{{ $staffMember->nin ?? '—' }}</dd>
                    <dt>Passport / Travel Document</dt><dd>{{ $staffMember->passport_number ?? '—' }}</dd>
                    <dt>Valid Until</dt><dd>{{ $staffMember->verification_expires_at?->format('d M Y') ?? '—' }}</dd>
                    @if($staffMember->reviewer)
                        <dt>Last Reviewed By</dt><dd class="mb-0">{{ $staffMember->reviewer->name }} ({{ $staffMember->reviewed_at?->format('d M Y') }})</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        @if($staffMember->correction_response)
            <div class="alert alert-info">
                <strong>Owner's correction response:</strong> {{ $staffMember->correction_response }}
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Documents</div>
            <div class="card-body p-0">
                @if($staffMember->documents->isEmpty())
                    <p class="text-muted text-center py-4 mb-0">No documents uploaded.</p>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>Type</th><th>File</th><th>Status</th><th class="text-end">Decision</th></tr></thead>
                            <tbody>
                                @foreach($staffMember->documents as $document)
                                    <tr>
                                        <td>{{ $document->documentType->name }}</td>
                                        <td>
                                            <a href="{{ route('admin.staff-documents.download', $document) }}">{{ $document->original_file_name }}</a>
                                            @if($document->admin_note)<br><small class="text-muted">{{ $document->admin_note }}</small>@endif
                                        </td>
                                        <td><span class="badge bg-{{ $docColors[$document->status] ?? 'secondary' }}">{{ ucfirst($document->status) }}</span></td>
                                        <td class="text-end">
                                            <form action="{{ route('admin.staff-documents.review', $document) }}" method="POST" class="d-inline-flex gap-1">
                                                @csrf
                                                <input type="text" name="note" class="form-control form-control-sm" placeholder="Note (optional)" maxlength="1000">
                                                <button type="submit" name="status" value="accepted" class="btn btn-sm btn-outline-success">Accept</button>
                                                <button type="submit" name="status" value="rejected" class="btn btn-sm btn-outline-danger">Reject</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        @if($staffMember->status === 'submitted')
            <div class="card">
                <div class="card-header bg-white fw-semibold">Review Decision</div>
                <div class="card-body">
                    <form action="{{ route('admin.staff.review', $staffMember) }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="decision" class="form-label">Decision</label>
                                <select name="decision" id="decision" class="form-select @error('decision') is-invalid @enderror" required>
                                    <option value="">Select decision</option>
                                    <option value="approved">Approve</option>
                                    <option value="correction_required">Request correction</option>
                                    <option value="rejected">Reject</option>
                                </select>
                                @error('decision') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-8">
                                <label for="note" class="form-label">Note to business owner</label>
                                <textarea name="note" id="note" rows="2" class="form-control @error('note') is-invalid @enderror">{{ old('note') }}</textarea>
                                @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Submit Decision</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
