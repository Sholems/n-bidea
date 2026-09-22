@extends('layouts.dashboard')

@section('title', 'Government Official Dashboard')

@section('content')
<h1 class="h3 mb-2">Government Official Dashboard</h1>
<p class="text-muted mb-4">Welcome back, {{ auth()->user()->name }}. Use the tools below to verify businesses and staff, and review your check history.</p>

<div class="row g-3 mb-4">
    <div class="col-md">
        <div class="card text-bg-primary h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Checks Today</h6>
                <h2 class="mb-0">{{ $counts['today'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card text-bg-info h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Checks This Week</h6>
                <h2 class="mb-0">{{ $counts['this_week'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card text-bg-secondary h-100">
            <div class="card-body">
                <h6 class="card-title text-uppercase opacity-75">Total Checks</h6>
                <h2 class="mb-0">{{ $counts['total'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Business and Staff Search</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('government.search.execute') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="query" class="form-label">Search by business name, registry number, staff name, or staff number</label>
                        <input type="text" name="query" id="query" class="form-control" placeholder="Enter search term..." required autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Recent Checks</h5>
            </div>
            <div class="card-body p-0">
                @if($recentChecks->isNotEmpty())
                @php
                    $actionLabels = [
                        'government_verification_check' => ['label' => 'Verification recorded', 'color' => 'success'],
                        'government_view_business' => ['label' => 'Business viewed', 'color' => 'info'],
                        'government_view_staff' => ['label' => 'Staff viewed', 'color' => 'primary'],
                    ];
                @endphp
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentChecks as $check)
                            @php $meta = $actionLabels[$check->action] ?? ['label' => $check->action, 'color' => 'secondary']; @endphp
                            <tr>
                                <td>{{ $check->description ?? 'N/A' }}</td>
                                <td><span class="badge bg-{{ $meta['color'] }}">{{ $meta['label'] }}</span></td>
                                <td>{{ $check->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-search fs-1 text-muted"></i>
                    <p class="text-muted mt-3 mb-0">No verification checks performed yet.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
