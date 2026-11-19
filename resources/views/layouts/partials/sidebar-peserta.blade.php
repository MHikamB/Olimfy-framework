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
       class="nav-link {{ request()->is('peserta/pendaftaran*', 'peserta/daftar*', 'peserta/invoice*') ? 'active' : '' }}">
        <i class="nav-icon bi bi-journal-check" aria-hidden="true"></i>
        <p>Pendaftaran Saya</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('peserta/submission') }}"
       class="nav-link {{ request()->is('peserta/submission*') ? 'active' : '' }}">
        <i class="nav-icon bi bi-cloud-arrow-up" aria-hidden="true"></i>
        <p>Pengumpulan Karya</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('peserta/bookmarks') }}"
       class="nav-link {{ request()->is('peserta/bookmarks*') ? 'active' : '' }}">
        <i class="nav-icon bi bi-bookmark-heart" aria-hidden="true"></i>
        <p>Kompetisi Disimpan</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ url('peserta/profil') }}"
       class="nav-link {{ request()->is('peserta/profil*') ? 'active' : '' }}">
        <i class="nav-icon bi bi-person-gear" aria-hidden="true"></i>
        <p>Profil Akun</p>
    </a>
</li>
