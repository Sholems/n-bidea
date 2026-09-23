@extends('layouts.public')

@section('title', 'Priority Sectors')
@section('meta_description', 'Explore the priority sectors driving trade, investment, industrial cooperation, and economic growth across the Nigeria-Benin corridor.')

@section('content')
<section class="public-page-hero">
    <div class="container py-5">
        <div class="row py-5">
            <div class="col-lg-8">
                <p class="section-kicker text-warning">Market Focus</p>
                <h1 class="display-5 fw-bold">Priority Sectors</h1>
                <p class="lead mb-0">Sector pathways for companies and investors working across the Nigeria-Benin trade corridor.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            @foreach(['Agribusiness', 'Manufacturing', 'Logistics and Transport', 'Energy', 'Tourism', 'Digital Trade'] as $sector)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <i class="bi bi-arrow-up-right-square text-ncci fs-3"></i>
                            <h5 class="mt-3">{{ $sector }}</h5>
                            <p class="text-muted mb-0">Dedicated sector content can include market data, trade routes, policy notes, opportunities, events, and verified company listings.</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
