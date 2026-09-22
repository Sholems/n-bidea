@extends('layouts.dashboard')

@section('title', 'My Businesses')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">My Businesses</h1>
    <a href="{{ route('business-owner.businesses.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Create New Business
    </a>
</div>

@if($businesses->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-building fs-1 text-muted"></i>
            <h5 class="text-muted mt-3">No Businesses Found</h5>
            <p class="text-muted">You haven't created any business profiles yet. Get started by creating your first business.</p>
            <a href="{{ route('business-owner.businesses.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Create Your First Business
            </a>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Business Name</th>
                            <th>Registration No.</th>
                            <th>Status</th>
                            <th>Sector</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
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
                        @foreach($businesses as $business)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $business->business_name }}</span>
                                    @if($business->trading_name)
                                        <br><small class="text-muted">Trading as {{ $business->trading_name }}</small>
                                    @endif
                                </td>
                                <td>{{ $business->registration_number ?? '—' }}</td>
                                <td>
                                    @php
                                        $color = $statusColors[$business->status] ?? 'secondary';
                                        $extraClass = $business->status === 'correction_required' ? ' text-dark' : '';
                                    @endphp
                                    <span class="badge bg-{{ $color }}{{ $extraClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $business->status)) }}
                                    </span>
                                </td>
                                <td>{{ $business->sector->name ?? '—' }}</td>
                                <td>{{ $business->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('business-owner.businesses.show', $business) }}" class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(in_array($business->status, ['approved', 'verified']))
                                            <a href="{{ route('business-owner.businesses.profile.show', $business) }}" class="btn btn-outline-success" title="Public Directory Listing">
                                                <i class="bi bi-shop"></i>
                                            </a>
                                        @endif
                                        @if(in_array($business->status, ['draft', 'correction_required']))
                                            <a href="{{ route('business-owner.businesses.edit', $business) }}" class="btn btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                        @if($business->status === 'draft')
                                            <form action="{{ route('business-owner.businesses.destroy', $business) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this business? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if($businesses->hasPages())
            <div class="card-footer bg-white">
                {{ $businesses->links() }}
            </div>
        @endif
    </div>
@endif
@endsection
