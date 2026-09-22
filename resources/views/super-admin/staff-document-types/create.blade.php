@extends('layouts.dashboard')

@section('title', 'Create Staff Document Type')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Create Staff Document Type</h1>
    <a href="{{ route('super-admin.staff-document-types.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to List</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('super-admin.staff-document-types.store') }}" method="POST">
            @csrf
            @include('super-admin.staff-document-types.form', ['staffDocumentType' => null])
            <hr class="my-4">
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Create Staff Document Type</button>
            </div>
        </form>
    </div>
</div>
@endsection
