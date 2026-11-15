@extends('layouts.backend')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan panel administrator PPDB SMAKNIS.')

@section('content')
    @php
        $kuotaService = app(\App\Services\KuotaService::class);
        $programs = \App\Models\Program::orderBy('kode')->get();
        $totalPendaftar = \App\Models\Pendaftaran::count();
        $diterima = \App\Models\Pendaftaran::where('status', 'selected')->count();
        $kuotaPenuh = $programs->filter(fn ($program) => $kuotaService->penuh($program))->count();
        $totalKuota = (int) $programs->sum('kuota');
        $sisaKuota = $programs->sum(fn ($program) => $kuotaService->sisa($program));
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="admin-stat h-100">
                <div class="stat-value">{{ $totalPendaftar }}</div>
                <div class="stat-label">Total Pendaftar</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat h-100">
                <div class="stat-value text-success">{{ $diterima }}</div>
                <div class="stat-label">Diterima</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat h-100">
                <div class="stat-value">{{ number_format($sisaKuota, 0, ',', '.') }}</div>
                <div class="stat-label">Sisa Kuota ({{ number_format($totalKuota, 0, ',', '.') }})</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat h-100">
                <div class="stat-value {{ $kuotaPenuh > 0 ? 'text-danger' : '' }}">{{ $kuotaPenuh }}</div>
                <div class="stat-label">Program Kuota Penuh</div>
            </div>
        </div>
    </div>

    <div class="admin-card mb-4">
        <div class="admin-card-header">
            <h2 class="h6 fw-bold mb-0">Menu Cepat</h2>
            <span class="text-muted-soft small">Pilih tindakan yang ingin dijalankan.</span>
        </div>
        <div class="admin-card-body">
            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('admin.verifikasi') }}" class="d-flex align-items-start gap-3 text-decoration-none h-100">
                        <span class="admin-stat d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.75rem; height: 2.75rem;">
                            <i class="bi bi-file-earmark-check fs-5 text-primary"></i>
                        </span>
                        <span>
                            <span class="d-block fw-semibold text-dark">Verifikasi Berkas</span>
                            <span class="d-block small text-muted-soft">Periksa berkas; kuota dialokasikan otomatis.</span>
                        </span>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('admin.kuota.index') }}" class="d-flex align-items-start gap-3 text-decoration-none h-100">
                        <span class="admin-stat d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.75rem; height: 2.75rem;">
                            <i class="bi bi-pie-chart fs-5 text-primary"></i>
                        </span>
                        <span>
                            <span class="d-block fw-semibold text-dark">Kelola Kuota</span>
                            <span class="d-block small text-muted-soft">Atur kuota tiap program keahlian.</span>
                        </span>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('admin.pendaftar.index') }}" class="d-flex align-items-start gap-3 text-decoration-none h-100">
                        <span class="admin-stat d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.75rem; height: 2.75rem;">
                            <i class="bi bi-people fs-5 text-primary"></i>
                        </span>
                        <span>
                            <span class="d-block fw-semibold text-dark">Semua Pendaftar</span>
                            <span class="d-block small text-muted-soft">Cari dan pantau status pendaftar.</span>
                        </span>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('admin.informasi.index') }}" class="d-flex align-items-start gap-3 text-decoration-none h-100">
                        <span class="admin-stat d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.75rem; height: 2.75rem;">
                            <i class="bi bi-megaphone fs-5 text-primary"></i>
                        </span>
                        <span>
                            <span class="d-block fw-semibold text-dark">Informasi PPDB</span>
                            <span class="d-block small text-muted-soft">Informasi publik yang tampil di beranda.</span>
                        </span>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('admin.jadwal.index') }}" class="d-flex align-items-start gap-3 text-decoration-none h-100">
                        <span class="admin-stat d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.75rem; height: 2.75rem;">
                            <i class="bi bi-calendar-event fs-5 text-primary"></i>
                        </span>
                        <span>
                            <span class="d-block fw-semibold text-dark">Jadwal PPDB</span>
                            <span class="d-block small text-muted-soft">Tahapan dan tanggal setiap kegiatan.</span>
                        </span>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('admin.laporan') }}" class="d-flex align-items-start gap-3 text-decoration-none h-100">
                        <span class="admin-stat d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.75rem; height: 2.75rem;">
                            <i class="bi bi-bar-chart fs-5 text-primary"></i>
                        </span>
                        <span>
                            <span class="d-block fw-semibold text-dark">Laporan</span>
                            <span class="d-block small text-muted-soft">Rekapitulasi dan export data.</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="h6 fw-bold mb-0">Kapasitas Program Keahlian</h2>
            <a href="{{ route('admin.kuota.index') }}" class="btn btn-sm btn-outline-primary fw-semibold">Kelola Kuota</a>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Program Keahlian</th>
                        <th style="width: 120px;">Kuota</th>
                        <th style="width: 120px;">Terisi</th>
                        <th style="width: 120px;">Sisa</th>
                        <th style="width: 220px;">Kapasitas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($programs as $program)
                        @php
                            $terpakai = $kuotaService->terpakai($program);
                            $sisa = $kuotaService->sisa($program);
                            $persentase = $program->kuota > 0 ? (int) round($terpakai / $program->kuota * 100) : 100;
                        @endphp
                        <tr>
                            <td><span class="badge text-bg-light border">{{ $program->kode }}</span></td>
                            <td class="fw-semibold">{{ $program->nama }}</td>
                            <td>{{ number_format($program->kuota, 0, ',', '.') }}</td>
                            <td>{{ number_format($terpakai, 0, ',', '.') }}</td>
                            <td>
                                <span class="fw-semibold {{ $sisa <= 0 ? 'text-danger' : 'text-success' }}">{{ number_format($sisa, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <div class="admin-quota-bar {{ $sisa <= 0 ? 'is-full' : '' }}">
                                    <span style="width: {{ min(100, $persentase) }}%"></span>
                                </div>
                                <small class="text-muted-soft">{{ $persentase }}% terisi</small>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
