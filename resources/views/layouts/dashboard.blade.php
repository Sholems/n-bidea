<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NB-CCI Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --ncci-primary: #063d1f;
            --ncci-secondary: #0f6b35;
            --ncci-accent: #ffd21e;
            --ncci-light: #e7f1e9;
            --ncci-dark: #082414;
            --sidebar-width: 260px;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            background-color: #f4f7f1;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--ncci-primary);
            color: #fff;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        .sidebar .brand {
            padding: 1.25rem 1rem;
            font-size: 1.25rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.75);
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255,255,255,0.08);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,0.12);
            border-left-color: var(--ncci-accent);
        }
        .sidebar .nav-section {
            padding: 0.75rem 1rem 0.5rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.4);
        }
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .top-navbar {
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .role-badge {
            font-size: 0.7rem;
            padding: 0.25em 0.6em;
        }
        .content-area {
            flex: 1;
            padding: 1.5rem;
        }
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--ncci-primary);
            cursor: pointer;
        }
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar-toggle {
                display: block;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
        .card {
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
        }
    </style>
    @include('layouts.partials.brand-styles')
    @stack('styles')
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <i class="bi bi-building"></i> NB-CCI
        </div>
        <nav class="mt-2">
            @php $role = Auth::user()->role; @endphp

            @if($role === 'business_owner')
                <div class="nav-section">Main</div>
                <a href="{{ route('business-owner.dashboard') }}" class="nav-link {{ request()->routeIs('business-owner.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('business-owner.businesses.index') }}" class="nav-link {{ request()->routeIs('business-owner.businesses.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> My Businesses
                </a>
                <a href="{{ route('public.directory.index') }}" class="nav-link">
                    <i class="bi bi-search"></i> Public Directory
                </a>
                <div class="nav-section">Management</div>
                <a href="{{ route('business-owner.renewals.index') }}" class="nav-link {{ request()->routeIs('business-owner.renewals.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-repeat"></i> Renewals
                </a>
                <a href="{{ route('business-owner.fees.index') }}" class="nav-link {{ request()->routeIs('business-owner.fees.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card"></i> Fees
                </a>
            @endif

            @if($role === 'admin')
                <div class="nav-section">Main</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <div class="nav-section">Management</div>
                <a href="{{ route('admin.businesses.index') }}" class="nav-link {{ request()->routeIs('admin.businesses.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i> Applications
                </a>
                <a href="{{ route('admin.business-profiles.index') }}" class="nav-link {{ request()->routeIs('admin.business-profiles.*') ? 'active' : '' }}">
                    <i class="bi bi-shop"></i> Directory Listings
                </a>
                <a href="{{ route('admin.staff.index') }}" class="nav-link {{ request()->routeIs('admin.staff.*', 'admin.staff-documents.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i> Staff Clearance
                </a>
                <a href="{{ route('admin.renewals.index') }}" class="nav-link {{ request()->routeIs('admin.renewals.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-repeat"></i> Renewals
                </a>
                <a href="{{ route('admin.fees.index') }}" class="nav-link {{ request()->routeIs('admin.fees.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card"></i> Fees
                </a>
            @endif

            @if($role === 'super_admin')
                <div class="nav-section">Main</div>
                <a href="{{ route('super-admin.dashboard') }}" class="nav-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <div class="nav-section">Administration</div>
                <a href="{{ route('super-admin.users.index') }}" class="nav-link {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Users
                </a>
                <a href="{{ route('super-admin.sectors.index') }}" class="nav-link {{ request()->routeIs('super-admin.sectors.*') ? 'active' : '' }}">
                    <i class="bi bi-grid"></i> Sectors
                </a>
                <a href="{{ route('super-admin.posts.index') }}" class="nav-link {{ request()->routeIs('super-admin.posts.*') ? 'active' : '' }}">
                    <i class="bi bi-newspaper"></i> Posts
                </a>
                <a href="{{ route('super-admin.publications.index') }}" class="nav-link {{ request()->routeIs('super-admin.publications.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-richtext"></i> Publications
                </a>
                <a href="{{ route('admin.business-profiles.index') }}" class="nav-link {{ request()->routeIs('admin.business-profiles.*') ? 'active' : '' }}">
                    <i class="bi bi-shop"></i> Directory Listings
                </a>
                <a href="{{ route('super-admin.document-types.index') }}" class="nav-link {{ request()->routeIs('super-admin.document-types.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark"></i> Document Types
                </a>
                <a href="{{ route('super-admin.staff-document-types.index') }}" class="nav-link {{ request()->routeIs('super-admin.staff-document-types.*') ? 'active' : '' }}">
                    <i class="bi bi-person-vcard"></i> Staff Document Types
                </a>
                <a href="{{ route('admin.staff.index') }}" class="nav-link {{ request()->routeIs('admin.staff.*', 'admin.staff-documents.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i> Staff Clearance
                </a>
                <a href="{{ route('super-admin.agencies.index') }}" class="nav-link {{ request()->routeIs('super-admin.agencies.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> Agencies
                </a>
                <div class="nav-section">System</div>
                <a href="{{ route('super-admin.settings.index') }}" class="nav-link {{ request()->routeIs('super-admin.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i> Settings
                </a>
                <a href="{{ route('super-admin.audit-logs.index') }}" class="nav-link {{ request()->routeIs('super-admin.audit-logs.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i> Audit Logs
                </a>
                <a href="{{ route('super-admin.reports.index') }}" class="nav-link {{ request()->routeIs('super-admin.reports.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart"></i> Reports
                </a>
            @endif

            @if($role === 'government_official')
                <div class="nav-section">Main</div>
                <a href="{{ route('government.dashboard') }}" class="nav-link {{ request()->routeIs('government.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <div class="nav-section">Search</div>
                <a href="{{ route('government.search') }}" class="nav-link {{ request()->routeIs('government.search') ? 'active' : '' }}">
                    <i class="bi bi-search"></i> Business Search
                </a>
            @endif
        </nav>
    </aside>

    <div class="main-content">
        <header class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle me-3" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <h6 class="mb-0 text-muted d-none d-md-block">@yield('page-title', 'Dashboard')</h6>
            </div>
            <div class="d-flex align-items-center">
                <div class="text-end d-none d-sm-block me-3">
                    <div class="fw-semibold">{{ Auth::user()->name }}</div>
                    <small>
                        <span class="badge bg-primary role-badge">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</span>
                    </small>
                </div>
                <div class="dropdown">
                    <a href="#" class="text-decoration-none" data-bs-toggle="dropdown">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }
    </script>
    @stack('scripts')
</body>
</html>
