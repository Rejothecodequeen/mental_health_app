@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>My Appointments</h4>
    <a href="{{ route('appointments.create') }}" class="btn btn-primary">Book Appointment</a>
</div>
<table class="table table-striped">
    <thead><tr><th>Date</th><th>Counselor</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
        @foreach($appointments as $appt)
            <tr>
                <td>{{ $appt->start_time->format('d M Y, H:i') }}</td>
                <td>{{ $appt->counselor->name }}</td>
                <td>{{ ucfirst($appt->status) }}</td>
                <td>
                    <a href="{{ route('appointments.edit',$appt) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                    <form action="{{ route('appointments.destroy',$appt) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this appointment?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
