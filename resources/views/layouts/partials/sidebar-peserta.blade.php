<li class="nav-item">
    <a href="{{ url('peserta/dashboard') }}"
       class="nav-link {{ request()->is('peserta/dashboard') ? 'active' : '' }}">
        <i class="nav-icon bi bi-speedometer2" aria-hidden="true"></i>
        <p>Dashboard</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('peserta/lomba') }}"
       class="nav-link {{ request()->is('peserta/lomba*') ? 'active' : '' }}">
        <i class="nav-icon bi bi-search" aria-hidden="true"></i>
        <p>Jelajah Lomba</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('peserta/pendaftaran') }}"
       class="nav-link {{ request()->is('peserta/pendaftaran*') ? 'active' : '' }}">
        <i class="nav-icon bi bi-journal-check" aria-hidden="true"></i>
        <p>Pendaftaran Saya</p>
    </a>
</li>
