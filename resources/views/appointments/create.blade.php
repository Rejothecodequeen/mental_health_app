@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Book Appointment</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('appointments.store') }}" method="POST">
                @csrf
                @include('appointments.partials.form')
                <button class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Confirm Booking
                </button>
                <a href="{{ route('appointments.index') }}" class="btn btn-light">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
