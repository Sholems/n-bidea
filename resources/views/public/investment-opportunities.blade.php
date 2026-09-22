@extends('layouts.public')

@section('title', 'Investment Opportunities')

@section('content')
<section class="public-page-hero">
    <div class="container py-5">
        <div class="row py-5">
            <div class="col-lg-8">
                <p class="section-kicker text-warning">Investment Desk</p>
                <h1 class="display-5 fw-bold">Investment Opportunities</h1>
                <p class="lead mb-0">A future opportunity marketplace for bankable projects, sector briefs, and investor enquiries.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            @foreach(['Project Profiles', 'Investor Enquiries', 'Sector Pipelines'] as $item)
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5>{{ $item }}</h5>
                            <p class="text-muted mb-0">Structured content and workflow support for investment promotion under the N-BIDEA programme.</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
