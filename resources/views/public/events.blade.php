@extends('layouts.public')

@section('title', 'Events')

@section('content')
<section class="public-page-hero">
    <div class="container py-5">
        <div class="row py-5">
            <div class="col-lg-8">
                <p class="section-kicker text-warning">Engagement</p>
                <h1 class="display-5 fw-bold">Events</h1>
                <p class="lead mb-0">Trade missions, business forums, training sessions, and corridor networking programmes.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            @foreach(['Trade Missions', 'Business Forums', 'SME Academy', 'Investor Roundtables'] as $eventType)
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5>{{ $eventType }}</h5>
                            <p class="text-muted mb-0">Future event listings can support registration, attendance, reminders, and post-event resources.</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
