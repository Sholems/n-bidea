@extends('layouts.public')

@section('title', 'Verification Result')

@section('content')
<section class="py-5 bg-ncci-soft">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                            <div>
                                <p class="section-kicker mb-2">Verification Result</p>
                                <h1 class="h3 fw-bold mb-0">{{ $isValid ? 'Staff Clearance Verified' : 'Staff Clearance Not Valid' }}</h1>
                            </div>
                            <span class="badge {{ $isValid ? 'bg-success' : 'bg-danger' }} fs-6">{{ $isValid ? 'Valid' : 'Not Valid' }}</span>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Staff Name</p>
                                <div class="fw-semibold">{{ $staffMember->full_name }}</div>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Staff Number</p>
                                <div class="fw-semibold">{{ $staffMember->staff_number }}</div>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Employing Business</p>
                                <div class="fw-semibold">{{ $staffMember->business->business_name }}</div>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Status</p>
                                <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $staffMember->status)) }}</div>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Verified</p>
                                <div class="fw-semibold">{{ $staffMember->verified_at?->toFormattedDateString() ?? 'Not available' }}</div>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Expires</p>
                                <div class="fw-semibold">{{ $staffMember->verification_expires_at?->toFormattedDateString() ?? 'Not available' }}</div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <p class="text-muted mb-0">
                            This result intentionally shows only public verification fields. Private documents,
                            identity numbers, contact records, and administrative notes are not exposed.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
