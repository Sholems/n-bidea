@extends('layouts.dashboard')

@section('title', $publication->title)

@section('content')
<div class="card">
    <div class="card-body">
        <p class="text-muted">{{ $publication->category?->name ?? 'Uncategorized' }} | {{ ucfirst($publication->status) }}</p>
        <h1 class="h3">{{ $publication->title }}</h1>
        <p>{{ $publication->description }}</p>
        <p class="text-muted mb-0">{{ $publication->download_count }} downloads</p>
    </div>
</div>
@endsection
