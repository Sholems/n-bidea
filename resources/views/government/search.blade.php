@extends('layouts.dashboard')

@section('title', 'Search Results')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Search Results</h1>
        <small class="text-muted">
            {{ $results->total() }} {{ Str::plural('result', $results->total()) }} found
            @if($request->input('query'))
                for "<strong>{{ $request->input('query') }}</strong>"
            @endif
        </small>
    </div>
    <a href="{{ route('government.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('government.search.execute') }}" method="POST">
            @csrf
            <div class="input-group">
                <input type="text" name="query" class="form-control" placeholder="Search by business, registry no., CAC no., staff name or staff no..." value="{{ $request->input('query') ?? old('query') }}" required>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($results->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-search fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Businesses Found</h5>
                <p class="text-muted mb-0">No businesses found matching your search. Try different keywords or check the spelling.</p>
            </div>
        @else
            @php
                $statusColors = [
                    'draft' => 'secondary',
                    'submitted' => 'info',
                    'under_review' => 'warning',
                    'correction_required' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    'verified' => 'success',
                    'expired' => 'secondary',
                    'suspended' => 'danger',
                ];
            @endphp
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Business Name</th>
                            <th>Registry No.</th>
                            <th>CAC No.</th>
                            <th>State</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $business)
                            <tr>
                                <td>
                                    <a href="{{ route('government.businesses.show', $business) }}" class="text-decoration-none fw-semibold">
                                        {{ $business->business_name }}
                                    </a>
                                    @if($business->trading_name)
                                        <br><small class="text-muted">aka {{ $business->trading_name }}</small>
                                    @endif
                                </td>
                                <td>{{ $business->registry_number ?? '—' }}</td>
                                <td>{{ $business->cac_number ?? '—' }}</td>
                                <td>{{ $business->state ?? '—' }}</td>
                                <td>
                                    @php
                                        $color = $statusColors[$business->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $business->status)) }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('government.businesses.show', $business) }}" class="btn btn-sm btn-outline-primary">
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
    @if($results->hasPages())
        <div class="card-footer bg-white">
            {{ $results->links() }}
        </div>
    @endif
</div>

@if($request->input('query'))
    <div class="card mt-4">
        <div class="card-header bg-white fw-semibold">
            Staff Matches ({{ $staffResults->total() }})
        </div>
        <div class="card-body p-0">
            @if($staffResults->isEmpty())
                <p class="text-muted text-center py-4 mb-0">No staff members found matching your search.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Staff No.</th>
                                <th>Business</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staffResults as $staffMember)
                                <tr>
                                    <td class="fw-semibold">{{ $staffMember->full_name }}<br><small class="text-muted">{{ $staffMember->job_title }}</small></td>
                                    <td>{{ $staffMember->staff_number ?? '—' }}</td>
                                    <td>{{ $staffMember->business->business_name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $staffMember->status === 'approved' ? 'success' : 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $staffMember->status)) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('government.staff.show', $staffMember) }}" class="btn btn-sm btn-outline-primary">
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
        @if($staffResults->hasPages())
            <div class="card-footer bg-white">
                {{ $staffResults->links() }}
            </div>
        @endif
    </div>
@endif
@endsection
