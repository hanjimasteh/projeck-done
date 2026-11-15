<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Admin') - PPDB SMAKNIS</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div class="admin-shell">
        <header class="admin-topbar">
            <div class="admin-topbar-inner">
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <button class="admin-icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarAdmin" aria-controls="sidebarAdmin" aria-label="Buka menu">
                        <i class="bi bi-list"></i>
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="admin-brand">
                        <span class="admin-brand-mark"><i class="bi bi-mortarboard-fill"></i></span>
                        <span class="admin-brand-text">
                            <span class="admin-brand-title">PPDB SMAKNIS</span>
                            <span class="admin-brand-sub">Panel Administrator</span>
                        </span>
                    </a>
                </div>

                <form action="{{ route('admin.pencarian') }}" method="GET" class="admin-search">
                    <i class="bi bi-search"></i>
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari nama atau nomor pendaftaran"
                        aria-label="Cari nama atau nomor pendaftaran"
                    >
                </form>

                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <a href="{{ url('/') }}" class="btn btn-sm btn-outline-dark d-none d-md-inline-flex" target="_blank" rel="noopener">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Situs Publik
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-box-arrow-right me-1"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div class="admin-body">
            <aside class="admin-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebarAdmin" aria-labelledby="sidebarAdminLabel">
                <div class="offcanvas-header d-lg-none">
                    <h5 class="offcanvas-title admin-brand" id="sidebarAdminLabel">
                        <span class="admin-brand-mark"><i class="bi bi-mortarboard-fill"></i></span>
                        <span class="admin-brand-text">PPDB SMAKNIS</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarAdmin" aria-label="Tutup"></button>
                </div>

                <nav class="admin-nav">
                    <div class="admin-nav-label">Menu Utama</div>

                    <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->is('admin/dashboard') ? 'is-active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i> Dashboard
                    </a>

                    <div class="admin-nav-group {{ request()->is('admin/informasi*', 'admin/jadwal*', 'admin/persyaratan*') ? 'is-open' : '' }}">
                        <button class="admin-nav-group-head" type="button" data-bs-toggle="collapse" data-bs-target="#groupPpdb" aria-expanded="{{ request()->is('admin/informasi*', 'admin/jadwal*', 'admin/persyaratan*') ? 'true' : 'false' }}" aria-controls="groupPpdb">
                            <i class="bi bi-info-square-fill"></i>
                            <span>PPDB</span>
                            <i class="bi bi-chevron-down admin-chevron ms-auto"></i>
                        </button>
                        <div class="collapse {{ request()->is('admin/informasi*', 'admin/jadwal*', 'admin/persyaratan*') ? 'show' : '' }}" id="groupPpdb">
                            <a href="{{ route('admin.informasi.index') }}" class="admin-nav-sub {{ request()->is('admin/informasi*') ? 'is-active' : '' }}">Informasi PPDB</a>
                            <a href="{{ route('admin.jadwal.index') }}" class="admin-nav-sub {{ request()->is('admin/jadwal*') ? 'is-active' : '' }}">Jadwal PPDB</a>
                            <a href="{{ route('admin.persyaratan.index') }}" class="admin-nav-sub {{ request()->is('admin/persyaratan*') ? 'is-active' : '' }}">Persyaratan</a>
                        </div>
                    </div>

                    <div class="admin-nav-group {{ request()->is('admin/pendaftar*') ? 'is-open' : '' }}">
                        <button class="admin-nav-group-head" type="button" data-bs-toggle="collapse" data-bs-target="#groupPendaftar" aria-expanded="{{ request()->is('admin/pendaftar*') ? 'true' : 'false' }}" aria-controls="groupPendaftar">
                            <i class="bi bi-people-fill"></i>
                            <span>Pendaftar</span>
                            <i class="bi bi-chevron-down admin-chevron ms-auto"></i>
                        </button>
                        <div class="collapse {{ request()->is('admin/pendaftar*') ? 'show' : '' }}" id="groupPendaftar">
                            <a href="{{ route('admin.pendaftar.index') }}" class="admin-nav-sub {{ request()->is('admin/pendaftar') ? 'is-active' : '' }}">Semua Pendaftar</a>
                            @php($pendaftarTerbaru = \App\Models\Pendaftaran::latest('id')->first())
                            @if ($pendaftarTerbaru)
                                <a href="{{ route('admin.pendaftar.show', $pendaftarTerbaru->id) }}" class="admin-nav-sub {{ request()->is('admin/pendaftar/*') ? 'is-active' : '' }}">Detail Pendaftar</a>
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('admin.verifikasi') }}" class="admin-nav-link {{ request()->is('admin/verifikasi*') ? 'is-active' : '' }}">
                        <i class="bi bi-file-earmark-check-fill"></i> Verifikasi
                    </a>
                    <a href="{{ route('admin.kuota.index') }}" class="admin-nav-link {{ request()->is('admin/kuota*') ? 'is-active' : '' }}">
                        <i class="bi bi-pie-chart-fill"></i> Kuota
                    </a>
                    <a href="{{ route('admin.pengumuman.index') }}" class="admin-nav-link {{ request()->is('admin/pengumuman*') ? 'is-active' : '' }}">
                        <i class="bi bi-megaphone-fill"></i> Pengumuman
                    </a>
                    <a href="{{ route('admin.daftar-ulang') }}" class="admin-nav-link {{ request()->is('admin/daftar-ulang*') ? 'is-active' : '' }}">
                        <i class="bi bi-clipboard-check-fill"></i> Daftar Ulang
                    </a>
                    <a href="{{ route('admin.laporan') }}" class="admin-nav-link {{ request()->is('admin/laporan*') ? 'is-active' : '' }}">
                        <i class="bi bi-bar-chart-fill"></i> Laporan
                    </a>

                    <div class="admin-nav-label mt-2">Master Data</div>

                    {{-- Jalur Pendaftaran telah dihapus --}}
                    <a href="{{ route('admin.program.index') }}" class="admin-nav-link {{ request()->is('admin/program*') ? 'is-active' : '' }}">
                        <i class="bi bi-book-fill"></i> Program Keahlian
                    </a>
                    <a href="{{ route('admin.jenis-dokumen.index') }}" class="admin-nav-link {{ request()->is('admin/jenis-dokumen*') ? 'is-active' : '' }}">
                        <i class="bi bi-folder-fill"></i> Jenis Dokumen
                    </a>
                </nav>

                <div class="admin-sidebar-footer">
                    <span class="admin-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <div class="fw-semibold text-truncate">{{ auth()->user()->name ?? 'Administrator' }}</div>
                        <small class="text-muted-soft">Administrator</small>
                    </div>
                </div>
            </aside>

            <main class="admin-content">
                <div class="admin-page-header">
                    <div class="min-w-0">
                        <h1 class="admin-page-title">@yield('title', 'Dashboard')</h1>
                        @hasSection('subtitle')
                            <p class="admin-page-subtitle mb-0">@yield('subtitle')</p>
                        @endif
                    </div>
                    <div class="admin-page-actions">
                        @yield('actions')
                    </div>
                </div>

                <div class="px-3 px-lg-4 pb-4">
                    <x-alert fluid />
                    @yield('content')
                </div>

                <footer class="admin-footer">
                    <div class="px-3 px-lg-4 py-3 d-flex flex-column flex-md-row justify-content-between gap-2 small text-muted-soft">
                        <span>&copy; {{ date('Y') }} PPDB SMAKNIS &middot; Panel Administrator</span>
                        <span>Kuota menentukan penerimaan pendaftar</span>
                    </div>
                </footer>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
