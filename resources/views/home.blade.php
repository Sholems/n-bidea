@extends('layouts.public')

@section('title', 'N-BIDEA Platform - NB-CCI')
@section('meta_description', 'Connect with verified businesses, trade opportunities, investment resources, and institutional partners across the Nigeria-Benin commercial corridor through NB-CCI.')

@push('styles')
<style>
    .home-hero { min-height: min(760px, 82vh); display: flex; align-items: center; }
    .home-hero h1 { max-width: 980px; }
    .home-hero .lead { max-width: 760px; }
    .home-stat { border-left: 3px solid var(--ncci-accent); padding-left: 1rem; }
    .audience-link { border-top: 3px solid var(--ncci-primary); transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .audience-link:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(8, 36, 20, 0.12); }
    .process-number { width: 2.5rem; height: 2.5rem; border-radius: 50%; background: var(--ncci-primary); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; }
    .sector-list { border-top: 1px solid var(--ncci-border); }
    .sector-list a { border-bottom: 1px solid var(--ncci-border); color: var(--ncci-ink); padding: 1rem 0; }
    .sector-list a:hover { color: var(--ncci-secondary); }
    .trust-band { background: var(--ncci-primary); color: #fff; }
    @media (max-width: 767.98px) {
        .home-hero { min-height: auto; }
        .home-hero .container { padding-top: 2rem !important; padding-bottom: 2rem !important; }
        .home-hero .container > div { padding-top: 0 !important; padding-bottom: 0 !important; }
        .home-hero .display-4 { font-size: 2rem; }
        .home-hero .lead { font-size: 1rem; }
        .home-hero .btn-lg { font-size: 0.95rem; padding: 0.65rem 0.85rem !important; }
    }
</style>
@endpush

@section('content')
<section class="public-page-hero home-hero">
    <div class="container py-5">
        <div class="py-4">
            <p class="section-kicker text-warning mb-3">Nigeria-Benin Business Integration</p>
            <h1 class="display-4 fw-bold mb-4">Nigeria-Benin Business Integration, Development and Economic Advancement Programme</h1>
            <p class="lead mb-4">One coordinated platform for credible business registration, verification, market intelligence, investment promotion, and cross-border trade support.</p>
            <div class="d-flex flex-wrap gap-3">
                @guest
                    <a href="{{ route('register') }}" class="btn btn-warning btn-lg px-4"><i class="bi bi-person-plus me-2"></i>Register Your Business</a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-warning btn-lg px-4"><i class="bi bi-speedometer2 me-2"></i>Go to Dashboard</a>
                @endguest
                <a href="{{ route('public.verification.index') }}" class="btn btn-outline-light btn-lg px-4"><i class="bi bi-patch-check me-2"></i>Verify a Business</a>
                <a href="{{ route('public.investment-opportunities') }}" class="btn btn-light btn-lg px-4"><i class="bi bi-graph-up-arrow me-2"></i>Explore Investment Opportunities</a>
            </div>
        </div>
    </div>
</section>

<section class="py-5 border-bottom">
    <div class="container">
        <div class="row g-4">
            @foreach([
                ['value' => '50,000+', 'label' => 'enterprises targeted for formalization and market access'],
                ['value' => '20,000+', 'label' => 'active platform users envisioned across the corridor'],
                ['value' => 'US$2B', 'label' => 'investment ambition supported by project promotion'],
                ['value' => number_format($platformStats['verifiedBusinesses']), 'label' => 'businesses currently verified on the platform'],
            ] as $stat)
                <div class="col-sm-6 col-lg-3">
                    <div class="home-stat h-100">
                        <div class="h2 fw-bold text-ncci mb-1">{{ $stat['value'] }}</div>
                        <p class="text-muted mb-0 small">{{ $stat['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container py-lg-3">
        <div class="row align-items-end mb-4">
            <div class="col-lg-7">
                <p class="section-kicker mb-2">Choose Your Path</p>
                <h2 class="fw-bold mb-2">Start with what you need to accomplish.</h2>
                <p class="text-muted mb-0">Purpose-built pathways connect each audience to the right records, opportunities, and support.</p>
            </div>
        </div>
        <div class="row g-3">
            @foreach([
                ['route' => 'register', 'icon' => 'bi-building-add', 'title' => 'Businesses', 'copy' => 'Register, submit documents, manage staff records, and maintain verification.'],
                ['route' => 'public.investment-opportunities', 'icon' => 'bi-briefcase', 'title' => 'Investors', 'copy' => 'Review corridor opportunities, sectors, and verified local partners.'],
                ['route' => 'public.verification.index', 'icon' => 'bi-shield-check', 'title' => 'Government & Agencies', 'copy' => 'Confirm registry and certificate status through trusted public records.'],
                ['route' => 'public.directory.index', 'icon' => 'bi-people', 'title' => 'Trade Partners', 'copy' => 'Discover verified suppliers, service providers, and market counterparts.'],
            ] as $audience)
                <div class="col-md-6 col-xl-3">
                    <a href="{{ route($audience['route']) }}" class="audience-link card h-100 text-decoration-none text-reset">
                        <div class="card-body p-4">
                            <i class="bi {{ $audience['icon'] }} fs-2 text-ncci"></i>
                            <h3 class="h5 mt-3">{{ $audience['title'] }}</h3>
                            <p class="text-muted mb-0">{{ $audience['copy'] }}</p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5 bg-ncci-soft">
    <div class="container py-lg-3">
        <div class="row g-5 align-items-start">
            <div class="col-lg-4">
                <p class="section-kicker mb-2">Trusted By Design</p>
                <h2 class="fw-bold">From registration to verified market visibility.</h2>
                <p class="text-muted mb-0">The portal combines administrative review with public verification and controlled business introductions.</p>
            </div>
            <div class="col-lg-8">
                <div class="row g-4">
                    @foreach([
                        ['01', 'Register', 'Create the business record and provide the required registration information.'],
                        ['02', 'Submit evidence', 'Upload business and staff documents through private, controlled workflows.'],
                        ['03', 'NB-CCI review', 'Reviewers assess the submission, request corrections, or grant approval.'],
                        ['04', 'Trade with confidence', 'Use public verification, directory discovery, and investment resources.'],
                    ] as [$number, $title, $copy])
                        <div class="col-md-6 d-flex gap-3">
                            <span class="process-number flex-shrink-0">{{ $number }}</span>
                            <div><h3 class="h5 mb-2">{{ $title }}</h3><p class="text-muted mb-0">{{ $copy }}</p></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container py-lg-3">
        <div class="row g-5">
            <div class="col-lg-6">
                <p class="section-kicker mb-2">Priority Economy</p>
                <h2 class="fw-bold mb-3">Trade sectors with corridor-wide potential.</h2>
                <p class="text-muted">Explore {{ number_format($platformStats['activeSectors']) }} active sectors represented in the portal and the programme areas shaping bilateral growth.</p>
                <a href="{{ route('public.priority-sectors') }}" class="btn btn-outline-ncci">Explore all priority sectors <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="col-lg-6">
                <div class="sector-list d-flex flex-column">
                    @foreach(['Agribusiness & Food Systems', 'Logistics & Cross-Border Trade', 'Manufacturing & Industrial Value Chains', 'Energy, Tourism & Digital Trade'] as $sector)
                        <a href="{{ route('public.priority-sectors') }}" class="d-flex justify-content-between text-decoration-none fw-semibold">
                            <span>{{ $sector }}</span><i class="bi bi-arrow-up-right"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="trust-band py-5">
    <div class="container py-lg-3">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <p class="section-kicker text-warning mb-2">Public Trust</p>
                <h2 class="fw-bold mb-2">Check a registry number before you engage.</h2>
                <p class="mb-0 text-white-50">Verification exposes approved public facts while keeping private owner and staff records protected.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('public.verification.index') }}" class="btn btn-warning btn-lg"><i class="bi bi-search me-2"></i>Verify Now</a>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container py-lg-3">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div><p class="section-kicker mb-2">Knowledge Hub</p><h2 class="fw-bold mb-0">Latest corridor insights</h2></div>
            <a href="{{ route('public.resources') }}" class="btn btn-outline-ncci">View all resources</a>
        </div>
        <div class="row g-4">
            @forelse($latestPosts as $post)
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 border">
                        <div class="card-body p-4">
                            <span class="section-kicker">{{ $post->category?->name ?? 'Insight' }}</span>
                            <h3 class="h5 mt-3"><a href="{{ route('public.blog.show', $post) }}" class="text-decoration-none text-reset">{{ $post->title }}</a></h3>
                            <p class="text-muted mb-3">{{ $post->excerpt }}</p>
                            <small class="text-muted">{{ $post->published_at?->toFormattedDateString() }}</small>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-lg-7"><div class="border-start border-4 border-warning ps-4 py-2"><h3 class="h5">Insights are being prepared.</h3><p class="text-muted mb-0">Visit the Knowledge Hub for reports, market guides, policy briefs, and upcoming articles.</p></div></div>
            @endforelse
        </div>
        @if($latestPublications->isNotEmpty())
            <div class="row g-3 mt-3">
                @foreach($latestPublications as $publication)
                    <div class="col-md-6"><a href="{{ route('public.publications.show', $publication) }}" class="d-flex align-items-center gap-3 border-top py-3 text-decoration-none text-reset"><i class="bi bi-file-earmark-pdf text-danger fs-3"></i><span><strong class="d-block">{{ $publication->title }}</strong><small class="text-muted">Report &amp; publication</small></span></a></div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
