@extends('layouts.public')

@section('title', $publication->title)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($publication->description), 160, ''))
@section('meta_type', 'article')

@section('content')
<section class="py-5 bg-ncci-soft">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <p class="section-kicker">{{ $publication->category?->name ?? 'Publication' }}</p>
                        <h1 class="display-6 fw-bold">{{ $publication->title }}</h1>
                        <p class="text-muted">{{ $publication->published_at?->toFormattedDateString() }} | {{ $publication->download_count }} downloads</p>
                        <p class="lead">{{ $publication->description }}</p>
                        <a href="{{ route('public.publications.download', $publication) }}" class="btn btn-ncci btn-lg">
                            <i class="bi bi-download me-2"></i>Download PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
