@extends('layouts.dashboard')

@section('title', $staffMember->full_name)

@section('content')
@php
    $valid = $staffMember->isValid();
    $docColors = ['pending' => 'warning', 'accepted' => 'success', 'rejected' => 'danger'];
@endphp
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">{{ $staffMember->full_name }}</h1>
        <small class="text-muted">{{ $staffMember->job_title }} &middot;
            <a href="{{ route('government.businesses.show', $staffMember->business) }}">{{ $staffMember->business->business_name }}</a>
        </small>
    </div>
    <a href="{{ route('government.search') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Search</a>
</div>

<div class="alert {{ $valid ? 'alert-success' : 'alert-danger' }} d-flex align-items-center">
    <i class="bi {{ $valid ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} fs-4 me-3"></i>
    <div>
        <strong>{{ $valid ? 'Cleared to cross' : 'Not cleared' }}</strong>
        <div class="small">
            Status: {{ ucfirst(str_replace('_', ' ', $staffMember->status)) }}
            @if($staffMember->verification_expires_at) &middot; Valid until {{ $staffMember->verification_expires_at->format('d M Y') }} @endif
            @if(! $staffMember->business->is_verified) &middot; Employing business is not currently verified @endif
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Identity</div>
            <div class="card-body">
                <dl class="mb-0 small">
                    <dt>Staff Number</dt><dd>{{ $staffMember->staff_number ?? '—' }}</dd>
                    <dt>Phone</dt><dd>{{ $staffMember->phone }}</dd>
                    <dt>Nationality</dt><dd>{{ $staffMember->nationality }}</dd>
                    <dt>Date of Birth</dt><dd>{{ $staffMember->date_of_birth?->format('d M Y') ?? '—' }}</dd>
                    <dt>NIN</dt><dd>{{ $staffMember->nin ?? '—' }}</dd>
                    <dt>Passport / Travel Document</dt><dd class="mb-0">{{ $staffMember->passport_number ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Documents on File</div>
            <ul class="list-group list-group-flush">
                @forelse($staffMember->documents as $document)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $document->documentType->name }}
                        <span class="badge bg-{{ $docColors[$document->status] ?? 'secondary' }}">{{ ucfirst($document->status) }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No documents on file.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
