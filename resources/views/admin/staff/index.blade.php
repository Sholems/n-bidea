@extends('layouts.dashboard')

@section('title', 'Staff Clearance')

@section('content')
@php
    $statusColors = [
        'draft' => 'secondary',
        'submitted' => 'info',
        'correction_required' => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
        'expired' => 'secondary',
    ];
@endphp
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Staff Clearance</h1>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.staff.index') }}" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search by name, staff number or business..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    @foreach(['submitted', 'correction_required', 'approved', 'rejected', 'expired', 'draft'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($staffMembers->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-people fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Staff Records Found</h5>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Business</th>
                            <th>Staff No.</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staffMembers as $staffMember)
                            <tr>
                                <td class="fw-semibold">{{ $staffMember->full_name }}<br><small class="text-muted">{{ $staffMember->job_title }}</small></td>
                                <td>{{ $staffMember->business->business_name }}</td>
                                <td>{{ $staffMember->staff_number ?? '—' }}</td>
                                <td><span class="badge bg-{{ $statusColors[$staffMember->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $staffMember->status)) }}</span></td>
                                <td>{{ $staffMember->updated_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.staff.show', $staffMember) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Review</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if($staffMembers->hasPages())
        <div class="card-footer bg-white">
            {{ $staffMembers->links() }}
        </div>
    @endif
</div>
@endsection
