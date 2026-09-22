@extends('layouts.public')

@section('title', 'Business Registry')

@section('content')
<section class="public-page-hero">
    <div class="container py-5">
        <div class="row py-5">
            <div class="col-lg-8">
                <p class="section-kicker text-warning">Verification</p>
                <h1 class="display-5 fw-bold">Business Registry</h1>
                <p class="lead mb-0">A public-facing entry point for verified business discovery and certificate validation.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h4">Verify or Discover a Business</h2>
                        <p class="text-muted">Search certificate and registry numbers, or browse admin-approved public profiles from verified corridor businesses.</p>
                        <a href="{{ route('public.verification.index') }}" class="btn btn-ncci me-2">
                            <i class="bi bi-search me-2"></i>Verify a Business
                        </a>
                        <a href="{{ route('public.directory.index') }}" class="btn btn-outline-ncci">
                            <i class="bi bi-shop me-2"></i>Browse Directory
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5>Directory Filters</h5>
                        <p class="text-muted mb-0">Search by business name, services, location, registry number, sector, and state.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
