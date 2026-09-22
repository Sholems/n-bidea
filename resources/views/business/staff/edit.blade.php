@extends('layouts.dashboard')

@section('title', 'Edit Staff Member')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Edit Staff Member</h1>
        <small class="text-muted">{{ $staffMember->business->business_name }}</small>
    </div>
    <a href="{{ route('business-owner.staff.show', $staffMember) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('business-owner.staff.update', $staffMember) }}" method="POST">
            @csrf
            @method('PUT')
            @include('business.staff.form')
            <hr class="my-4">
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
