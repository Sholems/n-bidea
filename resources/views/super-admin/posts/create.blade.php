@extends('layouts.dashboard')

@section('title', 'Create Post')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Create Post</h1>
    <a href="{{ route('super-admin.posts.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

@include('super-admin.posts.form', ['post' => null, 'action' => route('super-admin.posts.store'), 'method' => 'POST'])
@endsection
