@extends('layouts.public')

@section('title', 'Knowledge Hub')
@section('meta_description', 'Access NB-CCI trade reports, market insights, publications, guides, and practical resources for Nigeria-Benin cross-border commerce.')

@section('content')
<section class="public-page-hero">
    <div class="container py-5">
        <div class="row py-5">
            <div class="col-lg-8">
                <p class="section-kicker text-warning">Knowledge Hub</p>
                <h1 class="display-5 fw-bold">Resources</h1>
                <p class="lead mb-0">Reports, guides, policy briefs, and blog articles for businesses navigating Nigeria-Benin trade.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <p class="section-kicker">Latest Articles</p>
                <h2 class="fw-bold mb-0">Corridor Insights</h2>
            </div>
        </div>

        <div class="row g-4 mb-5">
            @forelse($posts as $post)
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('public.blog.show', $post) }}" class="text-decoration-none text-reset">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <span class="badge bg-ncci-soft text-ncci mb-3">{{ $post->category?->name ?? 'Insight' }}</span>
                                <h5>{{ $post->title }}</h5>
                                <p class="text-muted mb-3">{{ $post->excerpt }}</p>
                                <small class="text-muted">{{ $post->published_at?->toFormattedDateString() }}</small>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5 bg-ncci-soft">
                        <h5 class="text-muted">No published articles yet.</h5>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <p class="section-kicker">Reports And Publications</p>
                <h2 class="fw-bold mb-0">Downloadable Guidance</h2>
            </div>
        </div>

        <div class="row g-4">
            @forelse($publications as $publication)
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('public.publications.show', $publication) }}" class="text-decoration-none text-reset">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <i class="bi bi-file-earmark-pdf text-danger fs-2"></i>
                                <h5 class="mt-3">{{ $publication->title }}</h5>
                                <p class="text-muted mb-3">{{ $publication->description }}</p>
                                <small class="text-muted">{{ $publication->download_count }} downloads</small>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5 bg-ncci-soft">
                        <h5 class="text-muted">No published publications yet.</h5>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
