@extends('layouts.public')

@section('title', 'Verification Result')

@section('content')
<section class="py-5 bg-ncci-soft">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                @if($business)
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-lg-5">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                                <div>
                                    <p class="section-kicker mb-2">Verification Result</p>
                                    <h1 class="h3 fw-bold mb-0">
                                        @if($certificate)
                                            {{ $isValid ? 'Certificate Verified' : 'Certificate Not Valid' }}
                                        @else
                                            {{ $isValid ? 'Business Verified' : 'Business Not Valid' }}
                                        @endif
                                    </h1>
                                </div>
                                <span class="badge {{ $isValid ? 'bg-success' : 'bg-danger' }} fs-6">
                                    {{ $isValid ? 'Valid' : 'Not Valid' }}
                                </span>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Business Name</p>
                                    <div class="fw-semibold">{{ $business->business_name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Registry Number</p>
                                    <div class="fw-semibold">{{ $business->registry_number ?? 'Pending assignment' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Certificate Number</p>
                                    <div class="fw-semibold">{{ $certificate?->certificate_number ?? 'Certificate Pending Issuance' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Sector</p>
                                    <div class="fw-semibold">{{ $business->sector?->name ?? 'Not specified' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Status</p>
                                    <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $business->status)) }}</div>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Location</p>
                                    <div class="fw-semibold">{{ $business->city }}, {{ $business->state }}</div>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Issued</p>
                                    <div class="fw-semibold">{{ $certificate?->issued_at?->toFormattedDateString() ?? $business->verified_at?->toFormattedDateString() ?? 'Not available' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Expires</p>
                                    <div class="fw-semibold">{{ $certificate?->expires_at?->toFormattedDateString() ?? $business->verification_expires_at?->toFormattedDateString() ?? 'Not available' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Last Verification Date</p>
                                    <div class="fw-semibold">{{ $business->verified_at?->toFormattedDateString() ?? 'Not available' }}</div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <p class="text-muted mb-0">
                                This result intentionally shows only public verification fields. Private documents,
                                identity numbers, contact records, and administrative notes are not exposed.
                            </p>
                        </div>
                    </div>
                @else
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-lg-5 text-center">
                            <i class="bi bi-x-circle text-danger display-5"></i>
                            <h1 class="h4 fw-bold mt-3">No matching business or certificate was found.</h1>
                            <p class="text-muted mb-4">Check the number or code and try again.</p>
                            <a href="{{ route('public.verification.index') }}" class="btn btn-ncci">Try Another Search</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
