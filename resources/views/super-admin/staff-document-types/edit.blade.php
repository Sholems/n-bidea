@extends('layouts.dashboard')

@section('title', 'Edit Staff Document Type')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Edit Staff Document Type</h1>
    <a href="{{ route('super-admin.staff-document-types.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to List</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('super-admin.staff-document-types.update', $staffDocumentType) }}" method="POST">
            @csrf
            @method('PUT')
            @include('super-admin.staff-document-types.form')
            <hr class="my-4">
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
