<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'OLIMFY - Portal Kompetisi & Olimpiade Mahasiswa')</title>
    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: 1px;
            color: #0d6efd !important;
        }

        .footer {
            background-color: #1a1e21;
            color: #adb5bd;
        }
    </style>
    @stack('styles')
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- Navbar Publik -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fs-4" href="{{ route('home') }}">
                <i class="bi bi-trophy-fill me-1 text-warning"></i> OLIMFY
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto fw-medium">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active text-primary fw-bold' : '' }}" href="{{ route('home') }}">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('explore') ? 'active text-primary fw-bold' : '' }}" href="{{ route('explore') }}">Jelajah Lomba</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#cara-kerja">Cara Kerja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang-kami">Tentang Kami</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    @guest
                    <a href="{{ route('login') }}" class="btn btn-outline-primary px-3">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary px-3">Daftar</a>
                    @else
                    <span class="text-muted small me-2 d-none d-md-inline">Halo, <strong>{{ Auth::user()->name }}</strong></span>
                    <a href="{{ Auth::user()->role === 'penyelenggara' ? url('/panitia/dashboard') : route('peserta.dashboard') }}" class="btn btn-primary btn-sm px-3">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow-1">
        @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer Publik -->
    <footer id="tentang-kami" class="footer pt-5 pb-4 mt-5">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-5">
                    <h5 class="text-white fw-bold mb-3"><i class="bi bi-trophy-fill text-warning me-2"></i>OLIMFY</h5>
                    <p class="small">Platform manajemen dan katalog kompetisi akademik terpadu untuk pelajar, mahasiswa, dan penyelenggara event di seluruh Indonesia.</p>
                </div>
                <div class="col-lg-3">
                    <h6 class="text-white fw-bold mb-3">Navigasi Cepat</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('home') }}" class="text-decoration-none text-secondary">Beranda</a></li>
                        <li class="mb-2"><a href="{{ route('explore') }}" class="text-decoration-none text-secondary">Jelajah Kompetisi</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="text-white fw-bold mb-3">Kontak Bantuan</h6>
                    <p class="small mb-1"><i class="bi bi-envelope me-2"></i> support@olimfy.id</p>
                    <p class="small mb-1"><i class="bi bi-whatsapp me-2"></i> +62 812-3456-7890</p>
                    <p class="small"><i class="bi bi-geo-alt me-2"></i> Bandung & Surabaya, Indonesia</p>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="text-center small text-secondary">
                &copy; {{ date('Y') }} OLIMFY Framework. Hak Cipta Dilindungi.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>