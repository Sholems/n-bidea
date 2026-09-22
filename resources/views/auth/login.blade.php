@extends('layouts.app')

@section('title', 'Login - NB-CCI Portal')

@section('content')
<div class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <x-brand-logo variant="auth" class="mx-auto mb-3" />
                            <h3 class="mt-2 fw-bold">Welcome Back</h3>
                            <p class="text-muted">Sign in to your NB-CCI account</p>
                        </div>

                        @if(config('app.env') === 'local')
                            <div class="alert alert-info">
                                <div class="fw-semibold mb-2">Demo logins</div>
                                <div class="d-grid gap-2">
                                    @foreach([
                                        ['label' => 'Business Owner', 'email' => 'business@example.com'],
                                        ['label' => 'Super Admin', 'email' => 'admin@nb-cci.gov.ng'],
                                        ['label' => 'Admin Reviewer', 'email' => 'reviewer@nb-cci.gov.ng'],
                                        ['label' => 'Government Official', 'email' => 'official@nb-cci.gov.ng'],
                                    ] as $account)
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary text-start demo-login-button"
                                                data-demo-email="{{ $account['email'] }}"
                                                data-demo-password="password">
                                            <span class="fw-semibold">{{ $account['label'] }}</span>
                                            <span class="d-block small">{{ $account['email'] }} / password</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           required
                                           placeholder="you@example.com">
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           required
                                           autocomplete="current-password"
                                           placeholder="Enter your password">
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        Remember me
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </button>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="mb-2">
                                <a href="{{ route('password.request') }}" class="text-decoration-none">
                                    Forgot your password?
                                </a>
                            </p>
                            <p class="mb-0 text-muted">
                                Don't have an account?
                                <a href="{{ route('register') }}" class="text-decoration-none fw-semibold">
                                    Register here
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@if(config('app.env') === 'local')
    @push('scripts')
        <script>
            document.querySelectorAll('.demo-login-button').forEach((button) => {
                button.addEventListener('click', () => {
                    document.getElementById('email').value = button.dataset.demoEmail;
                    document.getElementById('password').value = button.dataset.demoPassword;
                });
            });
        </script>
    @endpush
@endif
