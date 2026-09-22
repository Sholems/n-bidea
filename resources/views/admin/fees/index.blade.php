@extends('layouts.dashboard')

@section('title', 'Fee Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Fee Management</h1>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($fees->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-credit-card fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Fees Found</h5>
                <p class="text-muted mb-0">There are no fee records to manage.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Business</th>
                            <th>Fee Type</th>
                            <th>Amount</th>
                            <th>Payment Status</th>
                            <th>Reference</th>
                            <th>Confirmed</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fees as $fee)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $fee->business->business_name ?? '—' }}</span>
                                </td>
                                <td>{{ ucfirst(str_replace('_', ' ', $fee->fee_type)) }}</td>
                                <td class="fw-semibold">₦{{ number_format($fee->amount, 2) }}</td>
                                <td>
                                    @if($fee->payment_status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($fee->payment_reference)
                                        <small>{{ $fee->payment_reference }}</small>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($fee->confirmed_by)
                                        <small class="text-muted">{{ $fee->confirmed_at ? $fee->confirmed_at->format('d M Y') : '—' }}</small>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($fee->payment_status !== 'paid')
                                        <form action="{{ route('admin.fees.confirm', $fee) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Confirm payment for this fee?');">
                                                <i class="bi bi-check-lg"></i> Confirm Payment
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">
                                            <i class="bi bi-check-circle-fill text-success"></i> Confirmed
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
    @if($fees->hasPages())
        <div class="card-footer bg-white">
            {{ $fees->links() }}
        </div>
    @endif
</div>
@endsection
