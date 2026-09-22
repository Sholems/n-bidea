@extends('layouts.dashboard')

@section('title', 'Staff - '.$business->business_name)

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
    <div>
        <h1 class="h3 mb-0">Staff &amp; Documents</h1>
        <small class="text-muted">{{ $business->business_name }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('business-owner.businesses.show', $business) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Business
        </a>
        @can('create', [App\Models\StaffMember::class, $business])
            <a href="{{ route('business-owner.businesses.staff.create', $business) }}" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Add Staff Member
            </a>
        @endcan
    </div>
</div>

<div class="alert alert-light border mb-4">
    <div class="d-flex gap-3">
        <i class="bi bi-file-earmark-arrow-up fs-3 text-primary"></i>
        <div>
            <h2 class="h6 mb-1">Staff Documents</h2>
            <p class="text-muted mb-0">Add a staff member first. Open a staff record to upload identity, travel, and supporting documents. Files are stored privately and reviewed individually.</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($staffMembers->isEmpty())
            <div class="text-center py-5">
                    <i class="bi bi-people fs-1 text-muted"></i>
                    <h5 class="text-muted mt-3">No Staff Added Yet</h5>
                    <p class="text-muted mb-3">Add every staff member who needs to cross the border. Each person is reviewed on their own documents.</p>
                    @can('create', [App\Models\StaffMember::class, $business])
                        <a href="{{ route('business-owner.businesses.staff.create', $business) }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Add First Staff Member</a>
                    @endcan
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Job Title</th>
                            <th>Staff No.</th>
                            <th>Documents</th>
                            <th>Status</th>
                            <th>Valid Until</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staffMembers as $staffMember)
                            <tr>
                                <td class="fw-semibold">{{ $staffMember->full_name }}</td>
                                <td>{{ $staffMember->job_title }}</td>
                                <td>{{ $staffMember->staff_number ?? '—' }}</td>
                                <td>{{ $staffMember->documents_count }}</td>
                                <td>
                                    <span class="badge bg-{{ $statusColors[$staffMember->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $staffMember->status)) }}</span>
                                </td>
                                <td>{{ $staffMember->verification_expires_at?->format('d M Y') ?? '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('business-owner.staff.show', $staffMember) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-folder2-open"></i> Open &amp; Upload
                                    </a>
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
