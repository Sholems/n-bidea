@extends('layouts.dashboard')

@section('title', 'Certificate')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between gap-3 mb-4">
                <div>
                    <h1 class="h3 mb-1">Business Certificate</h1>
                    <p class="text-muted mb-0">{{ $certificate->business->business_name }}</p>
                </div>
                <span class="badge {{ $certificate->isValid() ? 'bg-success' : 'bg-danger' }} align-self-start">
                    {{ $certificate->isValid() ? 'Valid' : 'Not Valid' }}
                </span>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <p class="text-muted mb-1">Certificate Number</p>
                    <div class="fw-semibold">{{ $certificate->certificate_number }}</div>
                </div>
                <div class="col-md-6">
                    <p class="text-muted mb-1">Registry Number</p>
                    <div class="fw-semibold">{{ $certificate->business->registry_number }}</div>
                </div>
                <div class="col-md-6">
                    <p class="text-muted mb-1">Issued</p>
                    <div class="fw-semibold">{{ $certificate->issued_at->toFormattedDateString() }}</div>
                </div>
                <div class="col-md-6">
                    <p class="text-muted mb-1">Expires</p>
                    <div class="fw-semibold">{{ $certificate->expires_at->toFormattedDateString() }}</div>
                </div>
            </div>

            <hr class="my-4">
            <p class="text-muted mb-2">Public verification URL</p>
            <a href="{{ $certificate->qr_payload }}" target="_blank" rel="noopener">{{ $certificate->qr_payload }}</a>
        </div>
    </div>
</div>
@endsection
