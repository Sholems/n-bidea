@extends('layouts.dashboard')

@section('title', 'Edit Business Profile')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Edit Business Profile</h1>
    <a href="{{ route('business-owner.businesses.show', $business) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Details
    </a>
</div>

<form action="{{ route('business-owner.businesses.update', $business) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-building me-2"></i>Business Information</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="business_name" class="form-label">Business Name <span class="text-danger">*</span></label>
                    <input type="text" name="business_name" id="business_name" class="form-control @error('business_name') is-invalid @enderror" value="{{ old('business_name', $business->business_name) }}" required>
                    @error('business_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="trading_name" class="form-label">Trading Name</label>
                    <input type="text" name="trading_name" id="trading_name" class="form-control @error('trading_name') is-invalid @enderror" value="{{ old('trading_name', $business->trading_name) }}">
                    @error('trading_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="registration_number" class="form-label">Registration Number</label>
                    <input type="text" name="registration_number" id="registration_number" class="form-control @error('registration_number') is-invalid @enderror" value="{{ old('registration_number', $business->registration_number) }}">
                    @error('registration_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="business_type" class="form-label">Business Type <span class="text-danger">*</span></label>
                    <select name="business_type" id="business_type" class="form-select @error('business_type') is-invalid @enderror" required>
                        <option value="">Select Business Type</option>
                        @foreach(['Sole Proprietorship', 'Partnership', 'Limited Liability Company', 'Corporation', 'NGO', 'Other'] as $type)
                            <option value="{{ $type }}" {{ old('business_type', $business->business_type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('business_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="sector_id" class="form-label">Sector <span class="text-danger">*</span></label>
                    <select name="sector_id" id="sector_id" class="form-select @error('sector_id') is-invalid @enderror" required>
                        <option value="">Select Sector</option>
                        @foreach($sectors as $sector)
                            <option value="{{ $sector->id }}" {{ old('sector_id', $business->sector_id) == $sector->id ? 'selected' : '' }}>{{ $sector->name }}</option>
                        @endforeach
                    </select>
                    @error('sector_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $business->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Location</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12">
                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                    <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $business->address) }}" required>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                    <input type="text" name="state" id="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state', $business->state) }}" required>
                    @error('state')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="lga" class="form-label">LGA <span class="text-danger">*</span></label>
                    <input type="text" name="lga" id="lga" class="form-control @error('lga') is-invalid @enderror" value="{{ old('lga', $business->lga) }}" required>
                    @error('lga')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="city" class="form-label">City</label>
                    <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $business->city) }}">
                    @error('city')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-telephone me-2"></i>Contact Information</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $business->phone) }}" required>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $business->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="website" class="form-label">Website</label>
                    <input type="url" name="website" id="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website', $business->website) }}" placeholder="https://">
                    @error('website')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12"><hr></div>
                <div class="col-12">
                    <h6 class="text-muted mb-3">Contact Person</h6>
                </div>
                <div class="col-md-4">
                    <label for="contact_person_name" class="form-label">Contact Person Name</label>
                    <input type="text" name="contact_person_name" id="contact_person_name" class="form-control @error('contact_person_name') is-invalid @enderror" value="{{ old('contact_person_name', $business->contact_person_name) }}">
                    @error('contact_person_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="contact_person_phone" class="form-label">Contact Person Phone</label>
                    <input type="text" name="contact_person_phone" id="contact_person_phone" class="form-control @error('contact_person_phone') is-invalid @enderror" value="{{ old('contact_person_phone', $business->contact_person_phone) }}">
                    @error('contact_person_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="contact_person_email" class="form-label">Contact Person Email</label>
                    <input type="email" name="contact_person_email" id="contact_person_email" class="form-control @error('contact_person_email') is-invalid @enderror" value="{{ old('contact_person_email', $business->contact_person_email) }}">
                    @error('contact_person_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Registration Details</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="cac_number" class="form-label">CAC Number</label>
                    <input type="text" name="cac_number" id="cac_number" class="form-control @error('cac_number') is-invalid @enderror" value="{{ old('cac_number', $business->cac_number) }}">
                    @error('cac_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="nrs_number" class="form-label">NRS Number</label>
                    <input type="text" name="nrs_number" id="nrs_number" class="form-control @error('nrs_number') is-invalid @enderror" value="{{ old('nrs_number', $business->nrs_number) }}">
                    @error('nrs_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="nin" class="form-label">NIN</label>
                    <input type="text" name="nin" id="nin" class="form-control @error('nin') is-invalid @enderror" value="{{ old('nin', $business->nin) }}">
                    @error('nin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-globe me-2"></i>Trade Information</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="trade_activity" class="form-label">Trade Activity</label>
                    <input type="text" name="trade_activity" id="trade_activity" class="form-control @error('trade_activity') is-invalid @enderror" value="{{ old('trade_activity', $business->trade_activity) }}">
                    @error('trade_activity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="border_route" class="form-label">Border Route</label>
                    <input type="text" name="border_route" id="border_route" class="form-control @error('border_route') is-invalid @enderror" value="{{ old('border_route', $business->border_route) }}">
                    @error('border_route')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('business-owner.businesses.show', $business) }}" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg"></i> Update Business
        </button>
    </div>
</form>
@endsection
