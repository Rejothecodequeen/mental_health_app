<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MUBAS Mental‑Health System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
    <script>
    

    // Apply token globally to all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });
</script>

</head>
<body class="bg-light">
<header class="navbar navbar-expand-lg navbar-dark bg-primary px-3">
    <a class="navbar-brand d-flex align-items-center" href="/">
        <img src="{{ asset('images/mubas-logo.png') }}" height="40" class="me-2" alt="MUBAS Logo">
        <span>University MH System</span>
    </a>
    <div class="ms-auto text-white">{{ Auth::user()->name }} ▾</div>
</header>
<div class="d-flex" style="min-height: 100vh;">
    <!-- Sidebar -->
    <nav class="bg-white border-end" style="width: 230px;">
        <ul class="nav flex-column py-4">
            <li class="nav-item"><a class="nav-link" href="#">Quick Chat</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('appointments.index') }}">Appointments</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Messages</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Self‑Assessments</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Settings</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow-1 p-4">
        @yield('content')
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>