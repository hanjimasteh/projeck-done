@extends('layouts.calon')

@section('title', 'Dashboard')
@section('subtitle', 'Ruang pendaftar PPDB SMAKNIS.')

@section('content')
    @php
        $status = $pendaftaran?->status;
        $rejected = $status === \App\Enums\StatusPendaftaran::REJECTED;
        $kuotaPenuh = $status === \App\Enums\StatusPendaftaran::KUOTA_PENUH;
        $diterima = $status === \App\Enums\StatusPendaftaran::SELECTED;
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="admin-stat h-100">
                <div class="stat-value">{{ $pendaftaran->nomor_pendaftaran ?? 'Belum ada' }}</div>
                <div class="stat-label">Nomor Pendaftaran</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat h-100">
                <div class="stat-value">{{ $pendaftaran?->ppdb?->tahun_ajaran ?? '-' }}</div>
                <div class="stat-label">Periode PPDB</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat h-100">
                <div class="stat-value text-primary">{{ $pendaftaran?->programPilihan1?->kode ?? '-' }}</div>
                <div class="stat-label">Pilihan Program 1</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat h-100">
                <div class="stat-value">{{ $pendaftaran?->programPilihan2?->kode ?? '-' }}</div>
                <div class="stat-label">Pilihan Program 2</div>
            </div>
        </div>
    </div>

    <div class="admin-card mb-4">
        <div class="admin-card-header">
            <h2 class="h6 fw-bold mb-0">Selamat datang, {{ auth()->user()->name }}</h2>
            <x-status-badge :status="$pendaftaran?->status" />
        </div>
        <div class="admin-card-body">
            @if ($rejected)
                <div class="alert alert-danger border-0 shadow-sm">
                    <div class="fw-semibold mb-1">
                        <i class="bi bi-exclamation-octagon-fill me-1"></i> Pendaftaran perlu diperbaiki
                    </div>
                    @if ($pendaftaran->keterangan)
                        <div>Catatan revisi: {{ $pendaftaran->keterangan }}</div>
                    @endif
                </div>
            @elseif ($kuotaPenuh)
                <div class="alert alert-warning border-0 shadow-sm">
                    <div class="fw-semibold mb-1">
                        <i class="bi bi-pie-chart-fill me-1"></i> Kuota program Anda penuh
                    </div>
                    <div>{{ $pendaftaran->keterangan ?? 'Silakan pilih program yang masih tersedia.' }}</div>
                </div>
            @elseif ($diterima)
                <div class="alert alert-success border-0 shadow-sm">
                    <div class="fw-semibold mb-1">
                        <i class="bi bi-check-circle-fill me-1"></i> Selamat, Anda Diterima
                    </div>
                    <div>Silakan lanjutkan ke proses daftar ulang.</div>
                </div>
            @endif

            @if (! $isOpen)
                <div class="alert alert-warning border-0 shadow-sm mb-0">
                    <i class="bi bi-clock-fill me-1"></i>{{ $closedMessage }}
                </div>
            @elseif ($locked)
                <div class="alert alert-info border-0 shadow-sm mb-0">
                    <i class="bi bi-lock-fill me-1"></i>Formulir sudah dikirim dan terkunci. Pantau status verifikasi secara berkala.
                </div>
            @endif
        </div>
    </div>

    <div class="admin-card mb-4">
        <div class="admin-card-header">
            <h2 class="h6 fw-bold mb-0">Fitur Pendaftar</h2>
            <span class="text-muted-soft small">Menu yang Anda butuhkan.</span>
        </div>
        <div class="admin-card-body">
            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ $canContinue ? route($nextRoute) : route('calon.pendaftaran.data-diri') }}" class="d-flex align-items-start gap-3 text-decoration-none h-100">
                        <span class="admin-stat d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.75rem; height: 2.75rem;">
                            <i class="bi bi-pencil-square fs-5 text-primary"></i>
                        </span>
                        <span>
                            <span class="d-block fw-semibold text-dark">{{ $rejected ? 'Perbaiki Formulir' : 'Lanjutkan Formulir' }}</span>
                            <span class="d-block small text-muted-soft">Data diri, orang tua, dan pilihan program.</span>
                        </span>
                    </a>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('calon.pendaftaran.dokumen') }}" class="d-flex align-items-start gap-3 text-decoration-none h-100">
                        <span class="admin-stat d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.75rem; height: 2.75rem;">
                            <i class="bi bi-cloud-arrow-up fs-5 text-primary"></i>
                        </span>
                        <span>
                            <span class="d-block fw-semibold text-dark">Unggah Dokumen</span>
                            <span class="d-block small text-muted-soft">Lengkapi berkas persyaratan.</span>
                        </span>
                    </a>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('calon.status') }}" class="d-flex align-items-start gap-3 text-decoration-none h-100">
                        <span class="admin-stat d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.75rem; height: 2.75rem;">
                            <i class="bi bi-clipboard-check fs-5 text-primary"></i>
                        </span>
                        <span>
                            <span class="d-block fw-semibold text-dark">Status Pendaftaran</span>
                            <span class="d-block small text-muted-soft">Pantau tahapan pendaftaran.</span>
                        </span>
                    </a>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('calon.hasil') }}" class="d-flex align-items-start gap-3 text-decoration-none h-100">
                        <span class="admin-stat d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.75rem; height: 2.75rem;">
                            <i class="bi bi-award fs-5 text-primary"></i>
                        </span>
                        <span>
                            <span class="d-block fw-semibold text-dark">Hasil Penerimaan</span>
                            <span class="d-block small text-muted-soft">Lihat pengumuman hasil.</span>
                        </span>
                    </a>
                </div>

                @if ($pendaftaran?->nomor_pendaftaran)
                    <div class="col-12 col-md-6 col-lg-4">
                        <a href="{{ route('calon.pendaftaran.cetak') }}" target="_blank" class="d-flex align-items-start gap-3 text-decoration-none h-100">
                            <span class="admin-stat d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.75rem; height: 2.75rem;">
                                <i class="bi bi-printer fs-5 text-primary"></i>
                            </span>
                            <span>
                                <span class="d-block fw-semibold text-dark">Cetak Bukti</span>
                                <span class="d-block small text-muted-soft">Bukti pendaftaran Anda.</span>
                            </span>
                        </a>
                    </div>
                @endif

                @if ($diterima)
                    <div class="col-12 col-md-6 col-lg-4">
                        <a href="{{ route('calon.daftar-ulang') }}" class="d-flex align-items-start gap-3 text-decoration-none h-100">
                            <span class="admin-stat d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.75rem; height: 2.75rem;">
                                <i class="bi bi-journal-check fs-5 text-primary"></i>
                            </span>
                            <span>
                                <span class="d-block fw-semibold text-dark">Daftar Ulang</span>
                                <span class="d-block small text-muted-soft">Lengkapi daftar ulang Anda.</span>
                            </span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="h6 fw-bold mb-0">Status Pendaftaran</h2>
            <span class="text-muted-soft small">Detail pengajuan Anda.</span>
        </div>
        <div class="admin-card-body">
            @if ($pendaftaran)
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted-soft fw-normal">Nomor Pendaftaran</dt>
                    <dd class="col-sm-8 fw-bold">{{ $pendaftaran->nomor_pendaftaran ?: 'Belum diterbitkan' }}</dd>

                    <dt class="col-sm-4 text-muted-soft fw-normal">Periode</dt>
                    <dd class="col-sm-8">{{ $pendaftaran->ppdb?->tahun_ajaran ?? '-' }}</dd>

                    <dt class="col-sm-4 text-muted-soft fw-normal">Pilihan Program 1</dt>
                    <dd class="col-sm-8">{{ $pendaftaran->programPilihan1?->nama ?? '-' }}</dd>

                    <dt class="col-sm-4 text-muted-soft fw-normal">Pilihan Program 2</dt>
                    <dd class="col-sm-8">{{ $pendaftaran->programPilihan2?->nama ?? '-' }}</dd>

                    <dt class="col-sm-4 text-muted-soft fw-normal">Tanggal Dikirim</dt>
                    <dd class="col-sm-8 mb-0">{{ $pendaftaran->tanggal_pendaftaran?->translatedFormat('d F Y H:i') ?? '-' }}</dd>
                </dl>
            @else
                <div class="admin-empty py-4">
                    <i class="bi bi-journal-plus fs-3 d-block mb-2"></i>
                    Anda belum memiliki data pendaftaran pada periode PPDB aktif.
                </div>
            @endif
        </div>
    </div>
@endsection