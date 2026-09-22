@extends('layouts.dashboard')

@section('title', 'Create Publication')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Create Publication</h1>
    <a href="{{ route('super-admin.publications.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

@include('super-admin.publications.form', ['publication' => null, 'action' => route('super-admin.publications.store'), 'method' => 'POST'])
@endsection
