@extends('layouts.backend')

@section('title', 'Laporan')
@section('subtitle', 'Rekapitulasi data pendaftar PPDB beserta filter dan export.')

@section('actions')
    <a href="{{ route('admin.laporan.export-csv', request()->query()) }}" class="btn btn-outline-dark">
        <i class="bi bi-filetype-csv me-1"></i> Export CSV
    </a>
    <a href="{{ route('admin.laporan.export-pdf', request()->query()) }}" class="btn btn-primary">
        <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
    </a>
@endsection

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="admin-stat">
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Pendaftar</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat">
                <div class="stat-value text-primary">{{ $stats['submitted'] }}</div>
                <div class="stat-label">Diajukan</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat">
                <div class="stat-value text-success">{{ $stats['verified'] }}</div>
                <div class="stat-label">Terverifikasi</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat">
                <div class="stat-value">{{ $stats['selected'] }}</div>
                <div class="stat-label">Diterima</div>
            </div>
        </div>
    </div>

    <div class="admin-card mb-3">
        <div class="admin-card-body">
            <form method="GET" action="{{ route('admin.laporan') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="tahun_ajaran" class="form-label fw-semibold small mb-1">Tahun Ajaran</label>
                    <select id="tahun_ajaran" name="tahun_ajaran" class="form-select form-select-sm">
                        <option value="">Semua Periode</option>
                        @foreach ($tahunAjaran as $tahun)
                            <option value="{{ $tahun }}" @selected(request('tahun_ajaran') === $tahun)>{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label for="status" class="form-label fw-semibold small mb-1">Status</label>
                    <select id="status" name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        @foreach ($statusOptions as $value)
                            <option value="{{ $value }}" @selected(request('status') === $value)>
                                {{ \App\Enums\StatusPendaftaran::tryFrom($value)?->label() ?? $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary fw-semibold">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.laporan') }}" class="btn btn-sm btn-outline-dark">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="h6 fw-bold mb-0">Rekapitulasi Data Pendaftar</h2>
            <button onclick="window.print()" class="btn btn-sm btn-outline-dark fw-semibold">
                <i class="bi bi-printer me-1"></i> Cetak
            </button>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No Pendaftaran</th>
                        <th>Nama Lengkap</th>
                        <th>NISN</th>
                        <th>Program 1</th>
                        <th style="width: 180px;">Status</th>
                        <th style="width: 140px;">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendaftaran as $item)
                        <tr>
                            <td><span class="badge text-bg-light border">{{ $item->nomor_pendaftaran ?? '-' }}</span></td>
                            <td class="fw-semibold">{{ $item->calonSiswa?->nama_lengkap ?? '-' }}</td>
                            <td>{{ $item->calonSiswa?->nisn ?? '-' }}</td>
                            <td>{{ $item->programPilihan1?->nama ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $item->status?->badgeClass() ?? 'text-bg-secondary' }}">
                                    {{ $item->status?->label() ?? '-' }}
                                </span>
                            </td>
                            <td>{{ $item->tanggal_pendaftaran?->translatedFormat('d M Y') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="admin-empty">
                                    <i class="bi bi-bar-chart fs-3 d-block mb-2"></i>
                                    Tidak ada data pendaftar sesuai filter.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pendaftaran->hasPages())
            <div class="p-3 border-top">{{ $pendaftaran->links() }}</div>
        @endif
    </div>
@endsection
