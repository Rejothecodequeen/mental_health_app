<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MUBAS Mental-Health System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Your compiled CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-color: #0d6efd;   /* Blue */
            --secondary-color: #20c997; /* Teal */
            --accent-color: #fd7e14;    /* Orange */
        }

        .sidebar .nav-link {
            color: #333;
            font-weight: 500;
        }
        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: #fff;
            border-radius: 0.375rem;
        }
        .sidebar .nav-link:hover {
            background-color: #e9f2ff;
            border-radius: 0.375rem;
        }

        /* Card header overrides */
        .card-header.bg-primary {
            background-color: var(--primary-color) !important;
        }
        .card-header.bg-info {
            background-color: var(--secondary-color) !important;
        }
        .card-header.bg-warning {
            background-color: var(--accent-color) !important;
        }

        /* Buttons */
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: #fff;
        }
    </style>
</head>
<body class="bg-light">
<header class="navbar navbar-expand-lg navbar-dark" style="background-color: var(--primary-color);">
    <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
        <img src="{{ asset('images/mubas_logo.jpg') }}" height="40" class="me-2" alt="MUBAS Logo">
        <span>University MH System</span>
    </a>
    <div class="ms-auto text-white dropdown">
        <a href="#" class="text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
            {{ Auth::user()->name }}
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item" type="submit">Logout</button>
                </form>
            </li>
        </ul>
    </div>
</header>

<div class="d-flex" style="min-height: 100vh;">
    <!-- Sidebar -->
    <nav class="sidebar bg-white border-end p-3" style="width: 230px;">
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a class="nav-link d-flex align-items-center {{ request()->routeIs('chat') ? 'active' : '' }}" href="{{ route('chat') }}">
                    <i class="bi bi-chat-dots me-2"></i> Quick Chat
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link d-flex align-items-center {{ request()->routeIs('appointments.*') ? 'active' : '' }}" href="{{ route('appointments.index') }}">
                    <i class="bi bi-calendar-check me-2"></i> Appointments
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link d-flex align-items-center {{ request()->routeIs('diary.*') ? 'active' : '' }}" href="{{ route('diary.index') }}">
                    <i class="bi bi-journal-text me-2"></i> Diary
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link d-flex align-items-center {{ request()->is('resources*') ? 'active' : '' }}" href="{{ url('/resources') }}">
                    <i class="bi bi-book me-2"></i> Resources
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link d-flex align-items-center" href="#">
                    <i class="bi bi-gear me-2"></i> Settings
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow-1 p-4">
        @yield('content')
    </main>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });
</script>

@stack('scripts')
</body>
</html>
