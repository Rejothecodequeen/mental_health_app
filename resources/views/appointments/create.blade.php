@extends('layouts.app')
@section('content')
<h4>Book Appointment</h4>
<form action="{{ route('appointments.store') }}" method="POST">
    @include('appointments.partials.form')
</form>
@endsection
