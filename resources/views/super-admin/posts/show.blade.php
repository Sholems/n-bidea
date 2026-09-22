@extends('layouts.dashboard')

@section('title', $post->title)

@section('content')
<div class="card">
    <div class="card-body">
        <p class="text-muted">{{ $post->category?->name ?? 'Uncategorized' }} | {{ ucfirst($post->status) }}</p>
        <h1 class="h3">{{ $post->title }}</h1>
        <p class="lead">{{ $post->excerpt }}</p>
        <div style="white-space: pre-line;">{{ $post->body }}</div>
    </div>
</div>
@endsection
