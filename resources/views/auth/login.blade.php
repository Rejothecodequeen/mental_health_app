@extends('layouts.guest') {{-- Uses your Bootstrap guest layout --}}

@section('content')
<div class="text-center mb-4">
    <img src="{{ asset('images/mubas_logo.jpg') }}" alt="MUBAS Logo" height="60" class="mb-2">
    <h2 class="fw-bold">University Mental Health System</h2>
    <p class="text-muted">Log in to your account</p>
</div>

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

    <!-- Email -->
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email"
               class="form-control @error('email') is-invalid @enderror"
               name="email" value="{{ old('email') }}" required autofocus>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Password -->
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password"
               class="form-control @error('password') is-invalid @enderror"
               name="password" required>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Remember Me -->
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
        <label class="form-check-label" for="remember_me">Remember me</label>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-decoration-none">
                Forgot your password?
            </a>
        @endif
        <button type="submit" class="btn btn-primary">Log in</button>
    </div>

    <div class="mt-3 text-center">
        <small>Don't have an account? <a href="{{ route('register') }}">Register here</a></small>
    </div>
</form>
@endsection
