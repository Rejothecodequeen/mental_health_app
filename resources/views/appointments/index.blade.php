@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">My Appointments</h3>
        <a href="{{ route('appointments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Book Appointment
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Counselor</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appt)
                        <tr>
                            <td>{{ $appt->start_time->format('d M Y, H:i') }} - {{ $appt->end_time->format('H:i') }}</td>
                            <td>{{ $appt->counselor->name }}</td>
                            <td>
                                <span class="badge bg-{{ $appt->status === 'booked' ? 'success' : ($appt->status === 'cancelled' ? 'danger' : 'secondary') }}">
                                    {{ ucfirst($appt->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('appointments.edit', $appt) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form action="{{ route('appointments.destroy', $appt) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this appointment?')">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No appointments yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
