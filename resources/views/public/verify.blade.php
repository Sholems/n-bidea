@extends('layouts.public')

@section('title', 'Verify a Certificate')
@section('meta_description', 'Verify an NB-CCI business registry number or certificate code through the official public verification service.')

@section('content')
<section class="public-page-hero">
    <div class="container py-5">
        <div class="row py-5">
            <div class="col-lg-8">
                <p class="section-kicker text-warning">Public Verification</p>
                <h1 class="display-5 fw-bold">Verify a Business or Certificate</h1>
                <p class="lead mb-0">Search by certificate number, registry number, or QR verification code.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('public.verification.search') }}">
                            @csrf
                            <label for="query" class="form-label fw-semibold">Certificate, registry, or verification code</label>
                            <div class="input-group input-group-lg">
                                <input type="text" name="query" id="query" value="{{ old('query') }}" class="form-control @error('query') is-invalid @enderror" placeholder="NBCCI-CERT-2026-000001" required>
                                <button type="submit" class="btn btn-ncci">
                                    <i class="bi bi-search me-2"></i>Verify
                                </button>
                                @error('query')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
