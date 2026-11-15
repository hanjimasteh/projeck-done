@php
    $menu = [
        ['label' => 'Jurusan', 'url' => route('jurusan'), 'active' => request()->is('jurusan')],
        ['label' => 'Alur', 'url' => route('alur'), 'active' => request()->is('alur')],
        ['label' => 'Jadwal', 'url' => route('jadwal'), 'active' => request()->is('jadwal')],
        ['label' => 'Pengumuman', 'url' => route('pengumuman'), 'active' => request()->is('pengumuman')],
    ];
@endphp

<nav class="navbar navbar-expand-lg navbar-ppdb sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
            <span class="navbar-brand-mark">
                <i class="bi bi-mortarboard-fill"></i>
            </span>
            <span>
                PPDB Online
                <span class="brand-sub">SMAKNIS</span>
            </span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUtama" aria-controls="navbarUtama" aria-expanded="false" aria-label="Buka navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarUtama">
            <ul class="navbar-nav mx-auto mb-3 mb-lg-0 gap-lg-1">
                @foreach ($menu as $item)
                    <li class="nav-item">
                        <a class="nav-link {{ $item['active'] ? 'active' : '' }}" href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                    </li>
                @endforeach
            </ul>

            <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2">
                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard Admin
                        </a>
                    @else
                        <a href="{{ route('calon.dashboard') }}" class="btn btn-outline-dark">
                            <i class="bi bi-person-workspace me-1"></i> Ruang Pendaftar
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="d-grid d-lg-block">
                        @csrf
                        <button type="submit" class="btn btn-outline-dark w-100">
                            <i class="bi bi-box-arrow-right me-1"></i> Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-dark">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus me-1"></i> Buat Akun
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
