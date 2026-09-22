@extends('layouts.dashboard')

@section('title', 'Government Official Dashboard')

@section('content')
<h1 class="h3 mb-2">Government Official Dashboard</h1>
<p class="text-muted mb-4">Welcome back, {{ auth()->user()->name }}. Use the tools below to verify businesses and review verification history.</p>

<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Business Search</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('government.search.execute') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="query" class="form-label">Search by business name, registration number, or CAC number</label>
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
                <h5 class="mb-0">Recent Verification Checks</h5>
            </div>
            <div class="card-body p-0">
                @if($recentChecks->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Business Name</th>
                                <th>Action</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentChecks as $check)
                            <tr>
                                <td>{{ $check->description ?? 'N/A' }}</td>
                                <td><span class="badge bg-info">{{ $check->action }}</span></td>
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
