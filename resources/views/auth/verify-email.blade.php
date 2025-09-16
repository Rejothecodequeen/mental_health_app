@extends('layouts.guest')

@section('content')
<div class="mb-3">
    <p class="text-muted">
        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? 
        If you didn't receive the email, we will gladly send you another.
    </p>
</div>

@if (session('status') == 'verification-link-sent')
    <div class="alert alert-success mb-3">
        A new verification link has been sent to the email address you provided during registration.
    </div>
@endif

<div class="d-flex justify-content-between mt-4">
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary">
            Resend Verification Email
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-link text-decoration-underline text-muted">
            Log Out
        </button>
    </form>
</div>
@endsection
