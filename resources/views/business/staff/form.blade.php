@php
    $field = fn (string $name) => old($name, $staffMember?->{$name});
@endphp
<div class="row g-3">
    <div class="col-md-6">
        <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
        <input type="text" name="full_name" id="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ $field('full_name') }}" required>
        @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label for="job_title" class="form-label">Job Title <span class="text-danger">*</span></label>
        <input type="text" name="job_title" id="job_title" class="form-control @error('job_title') is-invalid @enderror" value="{{ $field('job_title') }}" required>
        @error('job_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ $field('phone') }}" required>
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ $field('email') }}">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label for="nationality" class="form-label">Nationality <span class="text-danger">*</span></label>
        <input type="text" name="nationality" id="nationality" class="form-control @error('nationality') is-invalid @enderror" value="{{ $field('nationality') ?? 'Nigerian' }}" required>
        @error('nationality') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label for="date_of_birth" class="form-label">Date of Birth</label>
        <input type="date" name="date_of_birth" id="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $staffMember?->date_of_birth?->format('Y-m-d')) }}">
        @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label for="nin" class="form-label">NIN (11 digits)</label>
        <input type="text" name="nin" id="nin" class="form-control @error('nin') is-invalid @enderror" value="{{ $field('nin') }}" maxlength="11">
        @error('nin') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label for="passport_number" class="form-label">Passport / Travel Document No.</label>
        <input type="text" name="passport_number" id="passport_number" class="form-control @error('passport_number') is-invalid @enderror" value="{{ $field('passport_number') }}">
        @error('passport_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
