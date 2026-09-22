@extends('layouts.dashboard')

@section('title', 'Review Directory Listing')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Review Directory Listing</h1>
        <p class="text-muted mb-0">{{ $profile->business->business_name }}</p>
    </div>
    <a href="{{ route('admin.business-profiles.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

@php
    $statusColors = [
        'pending' => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
    ];
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-shop me-2"></i>Public Profile</h5>
                <span class="badge bg-{{ $statusColors[$profile->status] ?? 'secondary' }}">{{ ucfirst($profile->status) }}</span>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Summary</small>
                    <p class="mb-0">{{ $profile->summary }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Services</small>
                    <p class="mb-0">{{ $profile->services }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Operating Locations</small>
                    <p class="mb-0">{{ $profile->operating_locations }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Trade Interests</small>
                    <p class="mb-0">{{ $profile->trade_interests ?? '—' }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Certifications</small>
                    <p class="mb-0">{{ $profile->certifications ?? '—' }}</p>
                </div>
                <div>
                    <small class="text-muted d-block">Contact Preference</small>
                    <p class="mb-0">{{ $profile->contact_preference ?? 'NB-CCI controlled introduction' }}</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-envelope-paper me-2"></i>Interest Requests</h5>
            </div>
            <div class="card-body p-0">
                @if($profile->inquiries->isEmpty())
                    <div class="text-center py-4 text-muted">No interest requests yet.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Requester</th>
                                    <th>Company</th>
                                    <th>Interest</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($profile->inquiries as $inquiry)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">{{ $inquiry->requester_name }}</span>
                                            <br><small class="text-muted">{{ $inquiry->requester_email }}</small>
                                        </td>
                                        <td>{{ $inquiry->requester_company ?? '—' }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $inquiry->interest_type)) }}</td>
                                        <td><span class="badge bg-secondary">{{ ucfirst($inquiry->status) }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-building me-2"></i>Business Record</h5>
            </div>
            <div class="card-body">
                <small class="text-muted d-block">Owner</small>
                <div class="fw-semibold mb-3">{{ $profile->business->user->name ?? '—' }}</div>
                <small class="text-muted d-block">Registry Number</small>
                <div class="fw-semibold mb-3">{{ $profile->business->registry_number ?? '—' }}</div>
                <small class="text-muted d-block">Sector</small>
                <div class="fw-semibold mb-3">{{ $profile->business->sector->name ?? '—' }}</div>
                <small class="text-muted d-block">Location</small>
                <div class="fw-semibold">{{ $profile->business->city }}, {{ $profile->business->state }}</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Review Decision</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.business-profiles.update', $profile) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label for="decision" class="form-label">Decision</label>
                        <select name="decision" id="decision" class="form-select @error('decision') is-invalid @enderror">
                            <option value="approved">Approve</option>
                            <option value="rejected">Reject</option>
                        </select>
                        @error('decision')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="admin_note" class="form-label">Admin Note</label>
                        <textarea name="admin_note" id="admin_note" class="form-control @error('admin_note') is-invalid @enderror" rows="4">{{ old('admin_note', $profile->admin_note) }}</textarea>
                        @error('admin_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check2-circle"></i> Save Review
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
