@extends('layouts.dashboard')

@section('title', 'Add Staff Member')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Add Staff Member</h1>
        <small class="text-muted">{{ $business->business_name }}</small>
    </div>
    <a href="{{ route('business-owner.businesses.staff.index', $business) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Staff
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('business-owner.businesses.staff.store', $business) }}" method="POST">
            @csrf
            @include('business.staff.form', ['staffMember' => null])
            <hr class="my-4">
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Staff Member</button>
            </div>
        </form>
    </div>
</div>
@endsection
