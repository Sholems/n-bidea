@extends('layouts.dashboard')

@section('title', 'Audit Logs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Audit Logs</h1>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('super-admin.audit-logs.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="user" class="form-label">User</label>
                    <input type="text" name="user" id="user" class="form-control" placeholder="User name or email..." value="{{ request()->query('user') }}">
                </div>
                <div class="col-md-3">
                    <label for="action" class="form-label">Action</label>
                    <input type="text" name="action" id="action" class="form-control" placeholder="Action type..." value="{{ request()->query('action') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request()->query('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request()->query('date_to') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('super-admin.audit-logs.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($auditLogs->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-journal-text fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No Audit Logs Found</h5>
                <p class="text-muted">No audit logs match your current filters.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($auditLogs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('d M Y, H:i') }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $log->user->name ?? 'System' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $log->action }}</span>
                                </td>
                                <td>{{ $log->description ?? '—' }}</td>
                                <td><code>{{ $log->ip_address ?? '—' }}</code></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if($auditLogs->hasPages())
        <div class="card-footer bg-white">
            {{ $auditLogs->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
