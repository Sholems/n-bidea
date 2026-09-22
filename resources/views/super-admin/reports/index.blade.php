@extends('layouts.dashboard')

@section('title', 'Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Reports</h1>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Generate Report</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('super-admin.reports.generate') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="sector_id" class="form-label">Sector</label>
                    <select name="sector_id" id="sector_id" class="form-select">
                        <option value="">All Sectors</option>
                        @foreach($sectors as $sector)
                            <option value="{{ $sector->id }}">{{ $sector->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="state" class="form-label">State</label>
                    <select name="state" id="state" class="form-select">
                        <option value="">All States</option>
                        @foreach($states as $state)
                            <option value="{{ $state }}">{{ $state }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach(['draft', 'submitted', 'under_review', 'approved', 'rejected', 'verified', 'expired', 'suspended'] as $status)
                            <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" name="date_from" id="date_from" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" name="date_to" id="date_to" class="form-control">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-bar-chart me-2"></i> Generate Report
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
