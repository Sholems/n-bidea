@extends('layouts.public')

@section('title', 'Business Directory - NB-CCI')

@section('content')
<section class="public-page-hero py-5">
    <div class="container py-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="section-kicker text-warning mb-2">NB-CCI Business Directory</div>
                <h1 class="display-5 fw-bold mb-3">Find credible Nigeria-Benin corridor partners.</h1>
                <p class="lead mb-0">Browse approved NB-CCI listings and identify businesses that have completed an additional verification call or site visit.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-ncci-soft">
    <div class="container">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('public.directory.index') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-5">
                            <label for="query" class="form-label fw-semibold">Search</label>
                            <input type="text" name="query" id="query" class="form-control" value="{{ request('query') }}" placeholder="Business name, service, location, registry number">
                        </div>
                        <div class="col-lg-3">
                            <label for="sector_id" class="form-label fw-semibold">Sector</label>
                            <select name="sector_id" id="sector_id" class="form-select">
                                <option value="">All sectors</option>
                                @foreach($sectors as $sector)
                                    <option value="{{ $sector->id }}" @selected((string) request('sector_id') === (string) $sector->id)>
                                        {{ $sector->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label for="state" class="form-label fw-semibold">State</label>
                            <select name="state" id="state" class="form-select">
                                <option value="">All states</option>
                                @foreach($states as $state)
                                    <option value="{{ $state }}" @selected(request('state') === $state)>{{ $state }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 d-grid">
                            <button type="submit" class="btn btn-ncci">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($profiles->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-search fs-1 text-muted"></i>
                    <h5 class="text-muted mt-3">No Businesses Found</h5>
                    <p class="text-muted mb-0">Check the name or registry number, clear the filters, and try again.</p>
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach($profiles as $profile)
                    <div class="col-md-6 col-xl-4">
                        <article class="card h-100 border-0 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between gap-3 mb-3">
                                    <div class="d-flex gap-3">
                                        @if($profile->logo_path)
                                            <img src="{{ route('public.directory.logo', $profile) }}" alt="{{ $profile->business->business_name }} logo" class="border rounded flex-shrink-0" style="width: 64px; height: 64px; object-fit: contain; background: #fff;">
                                        @else
                                            <div class="border rounded d-flex align-items-center justify-content-center text-muted flex-shrink-0" style="width: 64px; height: 64px;">
                                                <i class="bi bi-building fs-4"></i>
                                            </div>
                                        @endif
                                        <div>
                                        <h2 class="h5 mb-1">{{ $profile->business->business_name }}</h2>
                                        <div class="text-muted small">{{ $profile->business->sector->name ?? 'General Trade' }}</div>
                                        </div>
                                    </div>
                                    @if($profile->business->is_verified)
                                        <span class="badge bg-success align-self-start"><i class="bi bi-patch-check-fill me-1"></i>Verified</span>
                                    @else
                                        <span class="badge bg-light text-dark border align-self-start">Registered</span>
                                    @endif
                                </div>
                                <p class="text-muted">{{ $profile->services }}</p>
                                <div class="small text-muted mb-3">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $profile->operating_locations }}
                                </div>
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <span class="small fw-semibold text-ncci">{{ $profile->business->registry_number }}</span>
                                    <a href="{{ route('public.directory.show', $profile) }}" class="btn btn-outline-ncci btn-sm">View Profile</a>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

            @if($profiles->hasPages())
                <div class="mt-4">
                    {{ $profiles->links() }}
                </div>
            @endif
        @endif
    </div>
</section>
@endsection
