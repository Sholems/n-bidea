@extends('layouts.dashboard')

@section('title', 'Edit Public Directory Listing')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Edit Public Directory Listing</h1>
        <p class="text-muted mb-0">{{ $business->business_name }}</p>
    </div>
    <a href="{{ route('business-owner.businesses.profile.show', $business) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="alert alert-light border">Your approved business name, registry number, sector, and general location are listed automatically. The fields below add public trade information and are reviewed before changes are published.</div>
        <form method="POST" action="{{ route('business-owner.businesses.profile.store', $business) }}" enctype="multipart/form-data">
            @csrf
            @php $profile = $business->profile; @endphp

            <div class="row g-3 align-items-center mb-4">
                <div class="col-auto">
                    @if($profile?->logo_path)
                        <img src="{{ route('business-owner.businesses.profile.logo', $business) }}" alt="{{ $business->business_name }} logo" class="border rounded" style="width: 96px; height: 96px; object-fit: contain; background: #fff;">
                    @else
                        <div class="border rounded d-flex align-items-center justify-content-center text-muted" style="width: 96px; height: 96px;">
                            <i class="bi bi-building fs-2"></i>
                        </div>
                    @endif
                </div>
                <div class="col">
                    <label for="logo" class="form-label fw-semibold">Business Logo</label>
                    <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">JPG, PNG, or WebP. Maximum 2 MB and 2400 x 2400 pixels.</div>
                </div>
            </div>

            <div class="mb-3">
                <label for="summary" class="form-label fw-semibold">Public Summary</label>
                <textarea name="summary" id="summary" class="form-control @error('summary') is-invalid @enderror" rows="4">{{ old('summary', $profile?->summary) }}</textarea>
                @error('summary')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="services" class="form-label fw-semibold">Services and Capabilities</label>
                <textarea name="services" id="services" class="form-control @error('services') is-invalid @enderror" rows="4">{{ old('services', $profile?->services) }}</textarea>
                @error('services')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="operating_locations" class="form-label fw-semibold">Operating Locations</label>
                    <textarea name="operating_locations" id="operating_locations" class="form-control @error('operating_locations') is-invalid @enderror" rows="3">{{ old('operating_locations', $profile?->operating_locations) }}</textarea>
                    @error('operating_locations')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="trade_interests" class="form-label fw-semibold">Trade Interests</label>
                    <textarea name="trade_interests" id="trade_interests" class="form-control @error('trade_interests') is-invalid @enderror" rows="3">{{ old('trade_interests', $profile?->trade_interests) }}</textarea>
                    @error('trade_interests')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row g-3 mt-0">
                <div class="col-md-6">
                    <label for="certifications" class="form-label fw-semibold">Certifications</label>
                    <input type="text" name="certifications" id="certifications" class="form-control @error('certifications') is-invalid @enderror" value="{{ old('certifications', $profile?->certifications) }}">
                    @error('certifications')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="website" class="form-label fw-semibold">Public Website</label>
                    <input type="url" name="website" id="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website', $profile?->website ?? $business->website) }}">
                    @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-3">
                <label for="contact_preference" class="form-label fw-semibold">Contact Preference</label>
                <input type="text" name="contact_preference" id="contact_preference" class="form-control @error('contact_preference') is-invalid @enderror" value="{{ old('contact_preference', $profile?->contact_preference) }}">
                @error('contact_preference')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('business-owner.businesses.profile.show', $business) }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send"></i> Submit for Review
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
