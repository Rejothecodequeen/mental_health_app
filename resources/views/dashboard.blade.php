@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <h4 class="mb-4">Welcome, {{ Auth::user()->name }}!</h4>
    <div class="row g-4">

        <!-- Upcoming Appointments -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Upcoming Appointments</div>
                <div class="card-body">
                    @foreach($appointments ?? [] as $appt)
                        <div>{{ $appt->start_time->format('d M, H:i') }} with {{ $appt->counselor->name }}</div>
                    @endforeach
                    <a href="{{ route('appointments.create') }}" class="btn btn-sm btn-outline-primary mt-2">Book New Appointment</a>
                    <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-secondary mt-2">View All Appointments</a>
                </div>
            </div>
        </div>

        <!-- Chat with Therapist -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">Chat with Therapist</div>
                <div class="card-body text-center">
                    <a href="{{ route('chat') }}" class="btn btn-info btn-sm">Open Chat</a>

                </div>
            </div>
        </div>

        <!-- Self‑Assessments -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Self‑Assessments</div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li><a href="#">PHQ‑9 Depression</a></li>
                        <li><a href="#">GAD‑7 Anxiety</a></li>
                        <li><a href="#">Stress Thermometer</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Diary / Personal Journal -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">Diary / Personal Journal</div>
                <div class="card-body text-center">
                    <a href="{{ route('diary.index') }}" class="btn btn-warning btn-sm">Open Diary</a>
                </div>
            </div>
        </div>

        <!-- Mental Health Resources -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">Mental Health Resources</div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0" id="resourcesList">
                        @foreach($resources ?? [] as $resource)
                            <li class="mb-2">
                                <strong>{{ $resource->title }}</strong><br>
                                @if($resource->description) <small>{{ $resource->description }}</small><br> @endif
                                @if($resource->type === 'link' && $resource->url)
                                    <a href="{{ $resource->url }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">Open Link</a>
                                @elseif($resource->file_path)
                                    <a href="{{ asset('storage/' . $resource->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">Download File</a>
                                @endif
                                @if(Auth::user()->role === 'therapist')
                                    <form method="POST" action="{{ route('resources.destroy', $resource->id) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger mt-1">Delete</button>
                                    </form>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    @if(Auth::user()->role === 'therapist')
                    <hr>
                    <h6>Upload New Resource</h6>
                    <form method="POST" action="{{ route('resources.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-2">
                            <input type="text" name="title" class="form-control" placeholder="Title" required>
                        </div>
                        <div class="mb-2">
                            <textarea name="description" class="form-control" placeholder="Description"></textarea>
                        </div>
                        <div class="mb-2">
                            <select name="type" class="form-control" required>
                                <option value="pdf">PDF</option>
                                <option value="doc">Document</option>
                                <option value="link">Link</option>
                                <option value="video">Video</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <input type="file" name="file" class="form-control">
                        </div>
                        <div class="mb-2">
                            <input type="url" name="url" class="form-control" placeholder="https://example.com">
                        </div>
                        <button type="submit" class="btn btn-success btn-sm">Upload Resource</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Emergency / Messages Icon -->
        <div class="col-md-6 d-flex flex-column justify-content-between">
            <div class="card shadow-sm mb-3 text-center border-danger">
                <div class="card-body">
                    <a href="tel:+265‑XXX‑XXXX" class="btn btn-danger btn-lg w-100">☎ Call Hotline</a>
                </div>
            </div>
            <div class="align-self-end">
                <a href="#" class="position-relative text-decoration-none">
                    <span class="fs-1">✉️</span>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                </a>
            </div>
        </div>

    </div>
</div>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-link">Logout</button>
</form>

@endsection
