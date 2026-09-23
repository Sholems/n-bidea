@extends('layouts.public')

@section('title', $post->title)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?: $post->body), 160, ''))
@section('meta_type', 'article')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <article class="col-lg-8">
                <a href="{{ route('public.resources') }}" class="btn btn-outline-ncci btn-sm mb-4">
                    <i class="bi bi-arrow-left me-2"></i>Resources
                </a>
                <p class="section-kicker">{{ $post->category?->name ?? 'Insight' }}</p>
                <h1 class="display-6 fw-bold">{{ $post->title }}</h1>
                <p class="text-muted">{{ $post->published_at?->toFormattedDateString() }}</p>
                @if($post->excerpt)
                    <p class="lead">{{ $post->excerpt }}</p>
                @endif
                <div class="mt-4 text-muted" style="white-space: pre-line;">{{ $post->body }}</div>
            </article>
        </div>
    </div>
</section>
@endsection
