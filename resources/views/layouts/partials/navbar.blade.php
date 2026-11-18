<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" aria-label="Buka atau tutup sidebar">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block">
                <a href="{{ url('/') }}" class="nav-link fw-semibold">OLIMFY</a>
            </li>
        </ul>

        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item">
                <span class="nav-link">
                    <i class="bi bi-person-circle me-1" aria-hidden="true"></i>
                    {{ Auth::user()?->name ?? 'Tamu' }}
                </span>
            </li>
            <li class="nav-item">
                <form method="POST" action="{{ Route::has('logout') ? route('logout') : url('/logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link">
                        <i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i>
                        Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav>
