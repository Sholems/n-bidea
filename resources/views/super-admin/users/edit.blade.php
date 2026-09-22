@extends('layouts.dashboard')

@section('title', 'Edit User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Edit User</h1>
    <a href="{{ route('super-admin.users.show', $user) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Details
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('super-admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="">Select Role</option>
                        @foreach(['super_admin', 'admin', 'business_owner', 'government_official'] as $role)
                            <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="account_status" class="form-label">Account Status <span class="text-danger">*</span></label>
                    <select name="account_status" id="account_status" class="form-select @error('account_status') is-invalid @enderror" required>
                        <option value="">Select Status</option>
                        @foreach(['active', 'pending', 'suspended'] as $status)
                            <option value="{{ $status }}" {{ old('account_status', $user->account_status) === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    @error('account_status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="agency_id" class="form-label">Agency</label>
                    <select name="agency_id" id="agency_id" class="form-select @error('agency_id') is-invalid @enderror">
                        <option value="">No Agency</option>
                        @if(isset($agencies))
                            @foreach($agencies as $agency)
                                <option value="{{ $agency->id }}" {{ old('agency_id', $user->agency_id) == $agency->id ? 'selected' : '' }}>{{ $agency->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('agency_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                    <small class="text-muted">Leave blank to keep current password.</small>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('super-admin.users.show', $user) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
