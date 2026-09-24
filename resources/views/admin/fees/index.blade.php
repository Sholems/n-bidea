@extends('layouts.dashboard')

@section('title', 'Fee Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Fee Management</h1>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.fees.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="status" class="form-label">Payment status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All payment statuses</option>
                    @foreach(['unpaid' => 'Unpaid', 'pending_confirmation' => 'Awaiting confirmation', 'paid' => 'Paid'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Apply Filter</button>
            </div>
            @if(request('status'))
                <div class="col-md-3">
                    <a href="{{ route('admin.fees.index') }}" class="btn btn-outline-secondary w-100">Clear Filter</a>
                </div>
            @endif
        </form>
    </div>
</div>

@php
    $statusColors = [
        'unpaid' => 'secondary',
        'pending_confirmation' => 'warning text-dark',
        'paid' => 'success',
    ];
@endphp

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
                                    <span class="badge bg-{{ $statusColors[$fee->payment_status] ?? 'secondary' }}">
                                        {{ $fee->payment_status === 'pending_confirmation' ? 'Awaiting confirmation' : ucfirst(str_replace('_', ' ', $fee->payment_status)) }}
                                    </span>
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
            {{ $fees->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
