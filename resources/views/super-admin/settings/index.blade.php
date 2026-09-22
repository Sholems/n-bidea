@extends('layouts.dashboard')

@section('title', 'Platform Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Platform Settings</h1>
</div>

<form action="{{ route('super-admin.settings.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Configuration</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Setting</th>
                            <th>Description</th>
                            <th style="min-width: 300px;">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($settings as $setting)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $setting->key }}</span>
                                </td>
                                <td class="text-muted">{{ $setting->description ?? '—' }}</td>
                                <td>
                                    @if($setting->type === 'boolean')
                                        <select name="settings[{{ $setting->key }}]" class="form-select form-select-sm">
                                            <option value="1" {{ $setting->value ? 'selected' : '' }}>Enabled</option>
                                            <option value="0" {{ !$setting->value ? 'selected' : '' }}>Disabled</option>
                                        </select>
                                    @elseif($setting->type === 'textarea')
                                        <textarea name="settings[{{ $setting->key }}]" class="form-control form-control-sm" rows="2">{{ $setting->value }}</textarea>
                                    @else
                                        <input type="text" name="settings[{{ $setting->key }}]" class="form-control form-control-sm" value="{{ $setting->value }}">
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg"></i> Save Settings
        </button>
    </div>
</form>
@endsection
