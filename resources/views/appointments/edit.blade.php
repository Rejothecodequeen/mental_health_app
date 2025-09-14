@extends('layouts.app')
@section('content')
<h4>Edit Appointment</h4>
<form action="{{ route('appointments.update', $appointment) }}" method="POST">
    @method('PUT')
    @include('appointments.partials.form')
</form>
@endsection
