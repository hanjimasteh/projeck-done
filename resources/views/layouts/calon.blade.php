<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Pendaftar') - PPDB SMAKNIS</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div class="app-shell d-flex">
        <aside class="offcanvas-lg offcanvas-start app-sidebar flex-shrink-0" tabindex="-1" id="sidebarCalon" aria-labelledby="sidebarCalonLabel" style="background-color: #1e293b; color: #cbd5e1;">
            <div class="offcanvas-header d-lg-none" style="background-color: #0f172a; border-bottom: 1px solid #334155;">
                <h5 class="offcanvas-title sidebar-brand" id="sidebarCalonLabel">
                    <i class="bi bi-mortarboard-fill text-warning"></i> PPDB SMAKNIS
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebarCalon" aria-label="Tutup"></button>
            </div>

            <div class="sidebar-brand d-none d-lg-flex px-4 pt-4 pb-3">
                <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-warning text-dark" style="width: 2.3rem; height: 2.3rem;">
                    <i class="bi bi-mortarboard-fill"></i>
                </span>
                <div>
                    <div class="lh-1">PPDB SMAKNIS</div>
                    <small class="fw-normal text-white-50" style="font-size: 0.72rem;">Ruang Calon Siswa</small>
                </div>
            </div>

            <div class="d-flex flex-column flex-grow-1 overflow-y-auto">
                <div class="sidebar-section" style="color: #a0aec0;">Menu Pendaftar</div>
                <nav class="sidebar-nav">
                    <a href="{{ route('calon.dashboard') }}" class="sidebar-link {{ request()->is('calon/dashboard') ? 'active' : '' }}" style="color: #cbd5e1;">
                        <i class="bi bi-grid-1x2-fill"></i> Dashboard
                    </a>
                    <a href="{{ route('calon.pendaftaran.data-diri') }}" class="sidebar-link {{ request()->is('calon/pendaftaran*', 'calon/formulir*') ? 'active' : '' }}" style="color: #cbd5e1;">
                        <i class="bi bi-file-earmark-text-fill"></i> Pendaftaran
                    </a>
                    <a href="{{ route('calon.status') }}" class="sidebar-link {{ request()->is('calon/status*') ? 'active' : '' }}" style="color: #cbd5e1;">
                        <i class="bi bi-clipboard2-pulse-fill"></i> Status
                    </a>
                    <a href="{{ route('calon.hasil') }}" class="sidebar-link {{ request()->is('calon/hasil*') ? 'active' : '' }}" style="color: #cbd5e1;">
                        <i class="bi bi-award-fill"></i> Hasil Penerimaan
                    </a>
                    <a href="{{ route('calon.daftar-ulang') }}" class="sidebar-link {{ request()->is('calon/daftar-ulang*') ? 'active' : '' }}" style="color: #cbd5e1;">
                        <i class="bi bi-journal-check"></i> Daftar Ulang
                    </a>
                    <a href="{{ route('calon.profil.edit') }}" class="sidebar-link {{ request()->is('calon/profil*') ? 'active' : '' }}" style="color: #cbd5e1;">
                        <i class="bi bi-person-circle"></i> Profil
                    </a>
                </nav>
            </div>

            <div class="sidebar-footer" style="border-top: 1px solid #334155; padding: 1rem 20px;">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar-circle" style="background-color: #334155; width: 32px; height: 32px;">
                        {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <div class="fw-semibold text-white text-truncate">{{ auth()->user()->name ?? 'Pendaftar' }}</div>
                        <small class="text-white-50">Calon Siswa</small>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm w-100" style="border-color: #a0aec0; color: #a0aec0;">
                        <i class="bi bi-box-arrow-right me-1"></i> Keluar
                    </button>
                </form>
            </div>
        </aside>

        <main class="app-main flex-grow-1 min-w-0">
            <div class="app-main-inner">
                <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-4">
                    <div>
                        <h1 class="h4 fw-bold text-dark mb-1">@yield('title', 'Dashboard')</h1>
                        @hasSection('subtitle')
                            <p class="text-muted mb-0 small">@yield('subtitle')</p>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ url('/') }}" class="btn btn-sm btn-outline-dark d-none d-md-inline-flex" target="_blank" rel="noopener">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Situs Publik
                        </a>
                        <span class="badge bg-primary">Calon Siswa</span>
                    </div>
                </div>

                <x-alert />

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
