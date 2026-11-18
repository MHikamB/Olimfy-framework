<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="{{ url('/') }}" class="brand-link">
            <i class="bi bi-trophy-fill me-2" aria-hidden="true"></i>
            <span class="brand-text fw-semibold">OLIMFY</span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2" aria-label="Navigasi utama">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                @if (Auth::user()?->role === 'penyelenggara')
                    @include('layouts.partials.sidebar-penyelenggara')
                @elseif (Auth::user()?->role === 'peserta')
                    @include('layouts.partials.sidebar-peserta')
                @endif
            </ul>
        </nav>
    </div>
</aside>
