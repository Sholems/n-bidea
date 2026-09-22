@extends('layouts.dashboard')

@section('title', 'Edit Document Type')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Edit Document Type</h1>
    <a href="{{ route('super-admin.document-types.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('super-admin.document-types.update', $documentType) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $documentType->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="">Select Status</option>
                        <option value="active" {{ old('status', $documentType->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $documentType->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $documentType->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_required" value="0">
                        <input type="checkbox" name="is_required" id="is_required" class="form-check-input" value="1" {{ old('is_required', $documentType->is_required) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_required">Required Document</label>
                    </div>
                    @error('is_required')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('super-admin.document-types.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Update Document Type
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
