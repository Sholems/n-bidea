@extends('layouts.public')

@section('title', $profile->business->business_name.' - NB-CCI Directory')

@section('content')
<section class="public-page-hero py-5">
    <div class="container py-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="section-kicker text-warning mb-2">Verified Business Listing</div>
                <h1 class="display-5 fw-bold mb-3">{{ $profile->business->business_name }}</h1>
                <p class="lead mb-0">{{ $profile->summary }}</p>
            </div>
            <div class="col-lg-4">
                <div class="bg-white text-dark rounded-2 p-4 shadow-sm">
                    <div class="small text-muted">Registry Number</div>
                    <div class="fw-bold mb-3">{{ $profile->business->registry_number }}</div>
                    <div class="small text-muted">Sector</div>
                    <div class="fw-bold mb-3">{{ $profile->business->sector->name ?? 'General Trade' }}</div>
                    <div class="small text-muted">Location</div>
                    <div class="fw-bold">{{ $profile->business->city }}, {{ $profile->business->state }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="mb-4">
                    <h2 class="h4 text-ncci">Services and Capabilities</h2>
                    <p>{{ $profile->services }}</p>
                </div>
                <div class="mb-4">
                    <h2 class="h4 text-ncci">Operating Locations</h2>
                    <p>{{ $profile->operating_locations }}</p>
                </div>
                @if($profile->trade_interests)
                    <div class="mb-4">
                        <h2 class="h4 text-ncci">Trade Interests</h2>
                        <p>{{ $profile->trade_interests }}</p>
                    </div>
                @endif
                @if($profile->certifications)
                    <div class="mb-4">
                        <h2 class="h4 text-ncci">Certifications</h2>
                        <p>{{ $profile->certifications }}</p>
                    </div>
                @endif
                @if($profile->website)
                    <a href="{{ $profile->website }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-ncci">
                        <i class="bi bi-box-arrow-up-right"></i> Visit Website
                    </a>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5 mb-3">Request Introduction</h2>
                        <p class="text-muted small">NB-CCI reviews interest requests before making any introduction. Private owner contact details remain protected.</p>
                        @auth
                            <form method="POST" action="{{ route('public.directory.inquiries.store', $profile) }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="requester_company" class="form-label">Company</label>
                                    <input type="text" name="requester_company" id="requester_company" class="form-control @error('requester_company') is-invalid @enderror" value="{{ old('requester_company') }}">
                                    @error('requester_company')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label for="interest_type" class="form-label">Interest Type</label>
                                    <select name="interest_type" id="interest_type" class="form-select @error('interest_type') is-invalid @enderror">
                                        @foreach(['buyer' => 'Buyer', 'supplier' => 'Supplier', 'distributor' => 'Distributor', 'investor' => 'Investor', 'service_provider' => 'Service Provider', 'other' => 'Other'] as $value => $label)
                                            <option value="{{ $value }}" @selected(old('interest_type') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('interest_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label for="requester_phone" class="form-label">Phone</label>
                                    <input type="text" name="requester_phone" id="requester_phone" class="form-control @error('requester_phone') is-invalid @enderror" value="{{ old('requester_phone') }}">
                                    @error('requester_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea name="message" id="message" class="form-control @error('message') is-invalid @enderror" rows="4">{{ old('message') }}</textarea>
                                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <button type="submit" class="btn btn-ncci w-100">
                                    <i class="bi bi-send"></i> Submit Request
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-ncci w-100">Login to Request Introduction</a>
                        @endauth
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h6 text-uppercase text-muted">Contact Preference</h2>
                        <p class="mb-0">{{ $profile->contact_preference ?? 'NB-CCI controlled introduction' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
