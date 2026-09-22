@extends('layouts.dashboard')

@section('title', 'Edit Post')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Edit Post</h1>
    <a href="{{ route('super-admin.posts.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

@include('super-admin.posts.form', ['post' => $post, 'action' => route('super-admin.posts.update', $post), 'method' => 'PUT'])
@endsection
