@extends('layouts.dashboard')

@section('title', 'Renewal Requests')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Renewal Requests</h1>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.renewals.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="status" class="form-label">Request status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All request statuses</option>
                    @foreach(['pending', 'approved', 'rejected'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Apply Filter</button>
            </div>
            @if(request('status'))
                <div class="col-md-3">
                    <a href="{{ route('admin.renewals.index') }}" class="btn btn-outline-secondary w-100">Clear Filter</a>
                </div>
            @endif
        </form>
    </div>
</div>

@php
    $statusColors = [
        'pending' => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
    ];
@endphp

<div class="card">
    <div class="card-body p-0">
        @if($renewalRequests->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-arrow-repeat fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Renewal Requests</h5>
                <p class="text-muted mb-0">There are no renewal requests to review.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Business</th>
                            <th>Requester</th>
                            <th>Status</th>
                            <th>Requested</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($renewalRequests as $request)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $request->business->business_name ?? '—' }}</span>
                                </td>
                                <td>{{ $request->requester->name ?? '—' }}</td>
                                <td>
                                    @php
                                        $color = $statusColors[$request->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ ucfirst($request->status) }}</span>
                                </td>
                                <td>{{ $request->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.renewals.show', $request) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if($renewalRequests->hasPages())
        <div class="card-footer bg-white">
            {{ $renewalRequests->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
