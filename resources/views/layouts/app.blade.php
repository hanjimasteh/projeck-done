<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB SMAKNIS - Platform Penerimaan Peserta Didik Baru</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <!-- Header / Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}"><i class="fa-solid fa-graduation-cap me-2"></i>PPDB SMAKNIS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Beranda</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <li class="nav-item"><a class="btn btn-warning me-2" href="{{ url('/admin/dashboard') }}">Dashboard Admin</a></li>
                        @else
                            <li class="nav-item"><a class="btn btn-success me-2" href="{{ url('/calon/dashboard') }}">Panel Siswa</a></li>
                        @endif
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-outline-light" type="submit">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item me-2"><a class="btn btn-outline-light" href="{{ url('/login') }}">Masuk</a></li>
                        <li class="nav-item"><a class="btn btn-warning fw-bold" href="{{ url('/register') }}">Daftar Akun</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="container my-4 flex-grow-1">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <div class="container">
            <p class="mb-0">&copy; 2026 SMAKNIS - Penerimaan Peserta Didik Baru Online</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>