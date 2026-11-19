<li class="nav-item">
    <a href="{{ url('penyelenggara/dashboard') }}"
       class="nav-link {{ request()->is('penyelenggara/dashboard') ? 'active' : '' }}">
        <i class="nav-icon bi bi-speedometer2" aria-hidden="true"></i>
        <p>Dashboard</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('penyelenggara/lomba') }}"
       class="nav-link {{ request()->is('penyelenggara/lomba*') ? 'active' : '' }}">
        <i class="nav-icon bi bi-trophy" aria-hidden="true"></i>
        <p>Manajemen Lomba</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('penyelenggara/validasi') }}"
       class="nav-link {{ request()->is('penyelenggara/validasi*') ? 'active' : '' }}">
        <i class="nav-icon bi bi-file-earmark-check" aria-hidden="true"></i>
        <p>Validasi Berkas</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('penyelenggara/submission') }}"
       class="nav-link {{ request()->is('penyelenggara/submission*') ? 'active' : '' }}">
        <i class="nav-icon bi bi-award" aria-hidden="true"></i>
        <p>Penilaian Karya</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('penyelenggara/laporan') }}"
       class="nav-link {{ request()->is('penyelenggara/laporan*') ? 'active' : '' }}">
        <i class="nav-icon bi bi-bar-chart-line" aria-hidden="true"></i>
        <p>Laporan Rekap</p>
    </a>
</li>
