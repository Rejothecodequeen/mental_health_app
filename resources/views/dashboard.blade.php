@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="mb-5">
        <h3 class="fw-bold">Welcome back, {{ Auth::user()->name }} 👋</h3>
        <p class="text-muted">Here’s an overview of your mental health activities and resources.</p>
    </div>

    <div class="row g-4">
        <!-- Upcoming Appointments -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header text-white" style="background-color: var(--primary-color);">
                    <i class="bi bi-calendar-check me-2"></i> Upcoming Appointments
                </div>
                <div class="card-body">
                    @forelse($appointments ?? [] as $appt)
                        <div class="mb-2">
                            <strong>{{ $appt->start_time->format('d M, H:i') }}</strong>
                            <span class="text-muted">with {{ $appt->counselor->name }}</span>
                        </div>
                    @empty
                        <p class="text-muted">No upcoming appointments.</p>
                    @endforelse
                    <div class="mt-3">
                        <a href="{{ route('appointments.create') }}" class="btn btn-sm btn-outline-primary">Book New</a>
                        <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat with Therapist -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header text-white" style="background-color: var(--secondary-color);">
                    <i class="bi bi-chat-dots me-2"></i> Chat with Therapist
                </div>
                <div class="card-body text-center">
                    <a href="{{ route('chat') }}" class="btn btn-sm text-white" style="background-color: var(--secondary-color);">
                        <i class="bi bi-arrow-right-circle me-1"></i> Open Chat
                    </a>
                </div>
            </div>
        </div>

        <!-- Self-Assessments -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header text-white" style="background-color: var(--primary-color);">
                    <i class="bi bi-clipboard2-pulse me-2"></i> Self-Assessments
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li><a href="#" class="text-decoration-none">📄 PHQ-9 Depression</a></li>
                        <li><a href="#" class="text-decoration-none">📄 GAD-7 Anxiety</a></li>
                        <li><a href="#" class="text-decoration-none">📄 Stress Thermometer</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Diary -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header text-white" style="background-color: var(--accent-color);">
                    <i class="bi bi-journal-text me-2"></i> Personal Diary
                </div>
                <div class="card-body text-center">
                    <a href="{{ route('diary.index') }}" class="btn btn-sm text-white" style="background-color: var(--accent-color);">
                        <i class="bi bi-journal me-1"></i> Open Diary
                    </a>
                </div>
            </div>
        </div>

        <!-- Mental Health Resources -->
<div class="col-lg-6">
    <div class="card shadow-sm border-0 h-100">
        <div class="card-header bg-success text-white">
            <i class="bi bi-book me-2"></i> Mental Health Resources
        </div>
        <div class="card-body text-center">
            <a href="{{ url('/resources') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-arrow-right-circle me-1"></i> View All Resources
            </a>
        </div>
    </div>
</div>


        <!-- Emergency / Messages -->
        <div class="col-lg-6 d-flex flex-column">
            <div class="card shadow-sm border-0 mb-3 text-center">
                <div class="card-body">
                    <a href="tel:+265-XXX-XXXX" class="btn btn-danger btn-lg w-100">
                        <i class="bi bi-telephone me-2"></i> Call Hotline
                    </a>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
