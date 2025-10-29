@extends('layouts.login_master')
@section('content')
<div class="d-flex justify-content-center align-items-center min-vh-100 p-3 position-relative"
    style="overflow: hidden;">
    <!-- Background Image with Overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="z-index: 0;">
        <div class="position-absolute top-0 start-0 w-100 h-100"
            style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.85) 0%, rgba(13, 202, 240, 0.75) 100%); z-index: 2;">
        </div>
        <img src="{{ asset('assets/img/school-background.jpg') }}" alt="School Background"
            class="w-100 h-100 object-fit-cover" style="z-index: 1;">
    </div>

    <!-- Floating Elements for Visual Interest -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="z-index: 1; pointer-events: none;">
        <div class="position-absolute rounded-circle bg-white opacity-10"
            style="width: 300px; height: 300px; top: -100px; right: -100px;"></div>
        <div class="position-absolute rounded-circle bg-white opacity-10"
            style="width: 200px; height: 200px; bottom: 50px; left: -50px;"></div>
        <div class="position-absolute rounded-circle bg-white opacity-5"
            style="width: 150px; height: 150px; top: 40%; left: 10%;"></div>
    </div>

    <!-- Login Card -->
    <div class="card shadow-lg border-0 rounded-4 position-relative"
        style="width: 100%; max-width: 420px; z-index: 3; backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.98);">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="mb-3">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="School Logo" width="80" class="mb-2">
                </div>
                <h4 class="fw-bold text-primary mb-1">Welcome Back!</h4>
                <p class="text-muted small mb-0">{{ config('app.name') }} - School Management System</p>
            </div>

            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Oops!</strong><br>
                {!! implode('<br>', $errors->all()) !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">
                        <i class="fa-solid fa-envelope me-1 text-primary"></i> Email / Login ID
                    </label>
                    <input type="text" name="email" id="email" class="form-control form-control-lg"
                        placeholder="Enter your email" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">
                        <i class="fa-solid fa-lock me-1 text-primary"></i> Password
                    </label>
                    <input type="password" name="password" id="password" class="form-control form-control-lg"
                        placeholder="Enter your password" required>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember')
                            ? 'checked' : '' }}>
                        <label class="form-check-label small" for="remember">
                            Remember me
                        </label>
                    </div>
                    <a href="{{ route('password.request') }}" class="text-decoration-none small text-primary">
                        Forgot Password?
                    </a>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fa-solid fa-right-to-bracket me-2"></i> Sign In
                    </button>
                </div>
            </form>

            <div class="text-center mt-4 pt-3 border-top">
                <p class="small text-muted mb-0">
                    <i class="fa-solid fa-graduation-cap me-1"></i>
                    © {{ date('Y') }} - {{ config('app.name') }} School. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Optional: Add animation to floating elements */
    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }
    }

    .position-absolute.rounded-circle {
        animation: float 6s ease-in-out infinite;
    }

    .position-absolute.rounded-circle:nth-child(2) {
        animation-delay: 2s;
        animation-duration: 8s;
    }

    .position-absolute.rounded-circle:nth-child(3) {
        animation-delay: 4s;
        animation-duration: 7s;
    }
</style>
@endsection