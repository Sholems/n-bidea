@extends('layouts.public')

@section('title', 'Contact NB-CCI')

@section('content')
<section class="public-page-hero">
    <div class="container py-5">
        <div class="row py-5">
            <div class="col-lg-8">
                <p class="section-kicker text-warning">Contact</p>
                <h1 class="display-5 fw-bold">Contact NB-CCI</h1>
                <p class="lead mb-0">Reach the chamber for registration support, verification questions, partnerships, events, and investment enquiries.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h4">Enquiry Channels</h2>
                        <p class="text-muted">A future contact module can route enquiries by category, attach supporting files, and assign tickets to chamber staff.</p>
                        <a href="{{ route('register') }}" class="btn btn-ncci">
                            <i class="bi bi-person-plus me-2"></i>Start Registration
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5>Suggested Enquiry Types</h5>
                        <ul class="mb-0">
                            <li>Business registration support</li>
                            <li>Certificate verification</li>
                            <li>Investment partnership</li>
                            <li>Training and event participation</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
