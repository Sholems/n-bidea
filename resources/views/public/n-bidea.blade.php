@extends('layouts.public')

@section('title', 'N-BIDEA Programme')

@section('content')
<section class="public-page-hero">
    <div class="container py-5">
        <div class="row py-5">
            <div class="col-lg-8">
                <p class="section-kicker text-warning">Programme Overview</p>
                <h1 class="display-5 fw-bold">N-BIDEA</h1>
                <p class="lead mb-0">A five-year Nigeria-Benin business integration, development, and economic advancement programme anchored by NB-CCI.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            @foreach([
                ['title' => 'Digital Business Infrastructure', 'copy' => 'A trusted registration, certification, verification, and business profile layer.'],
                ['title' => 'B2B Matchmaking', 'copy' => 'Company discovery, partner requests, trade missions, and corridor opportunity matching.'],
                ['title' => 'Investment Promotion', 'copy' => 'Curated project profiles, investor interest tracking, and sector-facing promotion pages.'],
                ['title' => 'Enterprise Support', 'copy' => 'Guides, advisory intake, training, women and youth desk, and export readiness resources.'],
            ] as $pillar)
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5>{{ $pillar['title'] }}</h5>
                            <p class="text-muted mb-0">{{ $pillar['copy'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
