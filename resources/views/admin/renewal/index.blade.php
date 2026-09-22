@extends('layouts.dashboard')

@section('title', 'Renewal Requests')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Renewal Requests</h1>
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
            {{ $renewalRequests->links() }}
        </div>
    @endif
</div>
@endsection
