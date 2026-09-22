@extends('layouts.dashboard')

@section('title', 'Fee & Payment Status')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Fee & Payment Status</h1>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($fees->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-credit-card fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Fees Found</h5>
                <p class="text-muted mb-0">There are no fee records for your businesses yet.</p>
            </div>
        @else
            @php
                $paymentStatusColors = [
                    'unpaid' => 'secondary',
                    'pending_confirmation' => 'warning',
                    'paid' => 'success',
                    'waived' => 'info',
                ];
            @endphp
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Business</th>
                            <th>Fee Type</th>
                            <th>Amount</th>
                            <th>Payment Status</th>
                            <th>Reference</th>
                            <th>Proof</th>
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
                                    @php
                                        $pColor = $paymentStatusColors[$fee->payment_status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $pColor }}">{{ ucfirst(str_replace('_', ' ', $fee->payment_status)) }}</span>
                                </td>
                                <td>
                                    @if($fee->payment_reference)
                                        <small>{{ $fee->payment_reference }}</small>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($fee->proof_file_path)
                                        <i class="bi bi-check-circle-fill text-success"></i> Uploaded
                                    @else
                                        <span class="text-muted">None</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if(in_array($fee->payment_status, ['unpaid', 'pending_confirmation']))
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadModal{{ $fee->id }}">
                                            <i class="bi bi-upload"></i> Upload Proof
                                        </button>
                                    @else
                                        <span class="text-muted small">
                                            <i class="bi bi-check-circle-fill text-success"></i> {{ ucfirst($fee->payment_status) }}
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

@foreach($fees as $fee)
    @if(in_array($fee->payment_status, ['unpaid', 'pending_confirmation']))
        <div class="modal fade" id="uploadModal{{ $fee->id }}" tabindex="-1" aria-labelledby="uploadModalLabel{{ $fee->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('business-owner.fees.upload-proof', $fee) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="uploadModalLabel{{ $fee->id }}">Upload Payment Proof</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Business</label>
                                <div class="fw-semibold">{{ $fee->business->business_name ?? '—' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Fee Type</label>
                                <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $fee->fee_type)) }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Amount</label>
                                <div class="fw-semibold">₦{{ number_format($fee->amount, 2) }}</div>
                            </div>
                            <div class="mb-3">
                                <label for="payment_reference{{ $fee->id }}" class="form-label fw-semibold">Payment Reference</label>
                                <input type="text" name="payment_reference" id="payment_reference{{ $fee->id }}" class="form-control" placeholder="Enter payment reference number" required>
                            </div>
                            <div class="mb-3">
                                <label for="proof_file{{ $fee->id }}" class="form-label fw-semibold">Proof of Payment</label>
                                <input type="file" name="proof_file" id="proof_file{{ $fee->id }}" class="form-control" accept="image/*,.pdf" required>
                                <div class="form-text">Accepted formats: Images (JPG, PNG) or PDF. Max size: 10MB</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload me-1"></i> Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endsection
