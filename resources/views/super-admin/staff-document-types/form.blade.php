@php
    $field = fn (string $name) => old($name, $staffDocumentType?->{$name});
@endphp
<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ $field('name') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="">Select Status</option>
            <option value="active" @selected($field('status') === 'active')>Active</option>
            <option value="inactive" @selected($field('status') === 'inactive')>Inactive</option>
        </select>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-12">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ $field('description') }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <div class="form-check form-switch">
            <input type="hidden" name="is_required" value="0">
            <input type="checkbox" name="is_required" id="is_required" class="form-check-input" value="1" @checked(old('is_required', $staffDocumentType?->is_required ?? true))>
            <label class="form-check-label" for="is_required">Required Document</label>
        </div>
    </div>
</div>
