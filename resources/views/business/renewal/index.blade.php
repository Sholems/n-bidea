@extends('layouts.dashboard')

@section('title', 'Renewal Requests')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Renewal Requests</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestRenewalModal">
        <i class="bi bi-plus-lg me-1"></i> Request Renewal
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($renewalRequests->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-arrow-repeat fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Renewal Requests</h5>
                <p class="text-muted mb-0">You haven't submitted any renewal requests yet.</p>
            </div>
        @else
            @php
                $statusColors = [
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    'completed' => 'success',
                ];
            @endphp
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Business</th>
                            <th>Status</th>
                            <th>Requested Date</th>
                            <th>New Expiry</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($renewalRequests as $request)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $request->business->business_name ?? '—' }}</span>
                                </td>
                                <td>
                                    @php
                                        $color = $statusColors[$request->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ ucfirst($request->status) }}</span>
                                </td>
                                <td>{{ $request->created_at->format('d M Y') }}</td>
                                <td>
                                    @if($request->new_expiry_date)
                                        {{ \Carbon\Carbon::parse($request->new_expiry_date)->format('d M Y') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($request->status === 'pending')
                                        <span class="badge bg-warning text-dark">Awaiting Review</span>
                                    @elseif($request->status === 'approved')
                                        <span class="text-success small">
                                            <i class="bi bi-check-circle-fill me-1"></i> Approved
                                        </span>
                                    @elseif($request->status === 'rejected')
                                        <span class="text-danger small">
                                            <i class="bi bi-x-circle-fill me-1"></i> Rejected
                                        </span>
                                    @endif
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

<div class="modal fade" id="requestRenewalModal" tabindex="-1" aria-labelledby="requestRenewalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('business-owner.renewals.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="requestRenewalModalLabel">Request Renewal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="business_id" class="form-label fw-semibold">Select Business</label>
                        <select name="business_id" id="business_id" class="form-select" required>
                            <option value="">Choose a business...</option>
                            @foreach($businesses as $biz)
                                <option value="{{ $biz->id }}" @disabled($biz->pending_renewal_count > 0)>
                                    {{ $biz->business_name }}{{ $biz->pending_renewal_count > 0 ? ' (pending request)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label fw-semibold">Reason for Renewal</label>
                        <textarea name="reason" id="reason" class="form-control" rows="3" placeholder="Explain why you need to renew...">{{ old('reason') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i> Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
