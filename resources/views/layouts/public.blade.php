<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NB-CCI Portal')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --ncci-primary: #063d1f;
            --ncci-secondary: #0f6b35;
            --ncci-accent: #ffd21e;
            --ncci-red: #d9270f;
            --ncci-ink: #172033;
            --ncci-muted: #6c7480;
            --ncci-soft: #f4f7f1;
            --ncci-dark: #082414;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--ncci-ink);
            background-color: #fff;
        }
        .navbar {
            background-color: var(--ncci-primary) !important;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #fff !important;
        }
        .navbar .nav-link {
            color: rgba(255,255,255,0.85) !important;
            font-weight: 500;
        }
        .navbar .nav-link:hover {
            color: #fff !important;
        }
        .navbar .dropdown-menu {
            border: 0;
            border-radius: 0.4rem;
            box-shadow: 0 12px 30px rgba(8, 36, 20, 0.18);
            min-width: 15rem;
            padding: 0.55rem;
        }
        .navbar .dropdown-item {
            border-radius: 0.3rem;
            color: var(--ncci-ink);
            padding: 0.65rem 0.75rem;
        }
        .navbar .dropdown-item:hover,
        .navbar .dropdown-item:focus {
            background-color: var(--ncci-soft);
            color: var(--ncci-primary);
        }
        .navbar .btn-warning {
            color: var(--ncci-dark);
            font-weight: 700;
        }
        .section-kicker {
            color: var(--ncci-secondary);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
        .text-ncci {
            color: var(--ncci-primary);
        }
        .bg-ncci-soft {
            background-color: var(--ncci-soft);
        }
        .btn-ncci {
            background-color: var(--ncci-primary);
            border-color: var(--ncci-primary);
            color: #fff;
            font-weight: 700;
        }
        .btn-ncci:hover {
            background-color: var(--ncci-secondary);
            border-color: var(--ncci-secondary);
            color: #fff;
        }
        .btn-outline-ncci {
            border-color: var(--ncci-primary);
            color: var(--ncci-primary);
            font-weight: 700;
        }
        .btn-outline-ncci:hover {
            background-color: var(--ncci-primary);
            color: #fff;
        }
        .metric-card {
            border-left: 4px solid var(--ncci-accent);
            background: #fff;
            box-shadow: 0 8px 24px rgba(8, 36, 20, 0.08);
        }
        .public-page-hero {
            background:
                linear-gradient(120deg, rgba(6, 61, 31, 0.94), rgba(15, 107, 53, 0.86)),
                url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1800&q=80');
            background-position: center;
            background-size: cover;
            color: #fff;
        }
        main {
            flex: 1;
        }
        footer {
            background-color: var(--ncci-dark);
            color: rgba(255,255,255,0.7);
            padding: 3.5rem 0 1.5rem;
        }
        footer h2 {
            color: #fff;
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        footer a {
            color: rgba(255,255,255,0.74);
            text-decoration: none;
        }
        footer a:hover {
            color: var(--ncci-accent);
        }
        .footer-brand {
            color: #fff;
            font-size: 1.3rem;
            font-weight: 800;
        }
        .footer-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .footer-list li + li {
            margin-top: 0.65rem;
        }
        @media (min-width: 992px) {
            .navbar .nav-link {
                padding-left: 0.7rem !important;
                padding-right: 0.7rem !important;
            }
        }
    </style>
    @include('layouts.partials.brand-styles')
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-building"></i> NB-CCI
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="publicNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">About</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('public.about') }}"><i class="bi bi-building me-2"></i>About NB-CCI</a></li>
                            <li><a class="dropdown-item" href="{{ route('public.n-bidea') }}"><i class="bi bi-diagram-3 me-2"></i>N-BIDEA Programme</a></li>
                            <li><a class="dropdown-item" href="{{ route('public.afcfta-ecowas') }}"><i class="bi bi-globe-africa me-2"></i>AfCFTA &amp; ECOWAS</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Explore</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('public.directory.index') }}"><i class="bi bi-search me-2"></i>Business Directory</a></li>
                            <li><a class="dropdown-item" href="{{ route('public.priority-sectors') }}"><i class="bi bi-grid me-2"></i>Priority Sectors</a></li>
                            <li><a class="dropdown-item" href="{{ route('public.investment-opportunities') }}"><i class="bi bi-graph-up-arrow me-2"></i>Investment Opportunities</a></li>
                            <li><a class="dropdown-item" href="{{ route('public.resources') }}"><i class="bi bi-journal-richtext me-2"></i>Insights &amp; Reports</a></li>
                            <li><a class="dropdown-item" href="{{ route('public.events') }}"><i class="bi bi-calendar-event me-2"></i>Events</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('public.verification.index') }}">Verify</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('public.contact') }}">Contact</a>
                    </li>
                    @auth
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-warning btn-sm mt-1" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                    @endauth
                    @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer>
        <div class="container">
            <div class="row g-4 pb-4">
                <div class="col-sm-6 col-lg-3">
                    <a class="footer-brand d-inline-block mb-3" href="{{ route('home') }}"><i class="bi bi-building me-2"></i>NB-CCI</a>
                    <p class="mb-0">Building trusted business connections, investment visibility, and practical trade support across the Nigeria-Benin corridor.</p>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <h2 class="mb-3">Platform</h2>
                    <ul class="footer-list">
                        <li><a href="{{ route('register') }}">Register a business</a></li>
                        <li><a href="{{ route('public.verification.index') }}">Verify a business</a></li>
                        <li><a href="{{ route('public.directory.index') }}">Business directory</a></li>
                        <li><a href="{{ route('public.registry') }}">Registry information</a></li>
                    </ul>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <h2 class="mb-3">Trade &amp; Investment</h2>
                    <ul class="footer-list">
                        <li><a href="{{ route('public.n-bidea') }}">N-BIDEA programme</a></li>
                        <li><a href="{{ route('public.priority-sectors') }}">Priority sectors</a></li>
                        <li><a href="{{ route('public.investment-opportunities') }}">Investment opportunities</a></li>
                        <li><a href="{{ route('public.afcfta-ecowas') }}">AfCFTA &amp; ECOWAS</a></li>
                    </ul>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <h2 class="mb-3">Resources &amp; Support</h2>
                    <ul class="footer-list">
                        <li><a href="{{ route('public.resources') }}">Insights and reports</a></li>
                        <li><a href="{{ route('public.events') }}">Events</a></li>
                        <li><a href="{{ route('public.contact') }}">Contact NB-CCI</a></li>
                        <li><a href="{{ route('login') }}">Portal login</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-top border-light border-opacity-25 pt-3 d-flex flex-wrap justify-content-between gap-2 small">
                <span>&copy; {{ date('Y') }} Nigeria-Benin Chamber of Commerce and Industry.</span>
                <span>Business Enumeration &amp; Verification Portal</span>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
