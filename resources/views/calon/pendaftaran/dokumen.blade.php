@extends('layouts.calon')

@section('title', 'Formulir Pendaftaran')
@section('subtitle', 'Langkah 4 dari 5 - Unggah berkas persyaratan')

@section('content')
    @include('calon.pendaftaran._stepper')

    @if ($pendaftaran?->status === \App\Enums\StatusPendaftaran::REJECTED)
        <div class="alert alert-danger border-0 shadow-sm d-flex gap-2">
            <i class="bi bi-exclamation-octagon-fill mt-1"></i>
            <div>
                <strong>Pendaftaran perlu diperbaiki.</strong>
                @if ($pendaftaran->keterangan)
                    <div class="mt-1">Catatan revisi: {{ $pendaftaran->keterangan }}</div>
                @endif
                <div class="small mt-1">Perbaiki dokumen yang ditolak atau unggah yang belum lengkap, lalu kirim ulang.</div>
            </div>
        </div>
    @endif

    @if ($locked)
        <div class="alert alert-info d-flex gap-2 border-0 shadow-sm">
            <i class="bi bi-lock-fill mt-1"></i>
            <div>Pendaftaran sudah dikirim dan terkunci. Dokumen hanya dapat dilihat.</div>
        </div>
    @elseif (! $editable)
        <div class="alert alert-warning d-flex gap-2 border-0 shadow-sm">
            <i class="bi bi-clock-fill mt-1"></i>
            <div>{{ $closedMessage }}</div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="admin-stat h-100">
                <div class="stat-value">{{ $wajibTotal }}</div>
                <div class="stat-label">Berkas Wajib</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat h-100">
                <div class="stat-value text-primary">{{ $wajibTerpenuhi }}</div>
                <div class="stat-label">Terunggah</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat h-100">
                <div class="stat-value {{ $wajibTerpenuhi === $wajibTotal ? 'text-success' : '' }}">{{ max(0, $wajibTotal - $wajibTerpenuhi) }}</div>
                <div class="stat-label">Belum Lengkap</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat h-100">
                <div class="stat-value">{{ $persenKelengkapan }}%</div>
                <div class="stat-label">Kelengkapan</div>
            </div>
        </div>
    </div>

    <div class="admin-card mb-4">
        <div class="admin-card-header">
            <div>
                <h2 class="h6 fw-bold mb-0">Unggah Dokumen Persyaratan</h2>
                <span class="text-muted-soft small">File disimpan secara privat; hanya Anda dan panitia PPDB yang dapat melihatnya.</span>
            </div>
            @if ($documentsComplete)
                <span class="badge text-bg-success px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Dokumen wajib lengkap</span>
            @else
                <span class="badge text-bg-warning px-3 py-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $missingRequired->count() }} belum lengkap</span>
            @endif
        </div>
        <div class="admin-card-body">
            <div class="d-flex justify-content-between align-items-center small fw-semibold mb-1">
                <span>Kelengkapan berkas wajib</span>
                <span>{{ $wajibTerpenuhi }} / {{ $wajibTotal }}</span>
            </div>
            <div class="admin-quota-bar {{ $documentsComplete ? '' : 'is-full' }}">
                <span style="width: {{ min(100, $persenKelengkapan) }}%"></span>
            </div>

            @if (! $documentsComplete)
                <div class="alert alert-warning border-0 mt-3 mb-0 small">
                    <div class="fw-semibold mb-1">Dokumen wajib yang belum lengkap:</div>
                    <ul class="mb-1 ps-3">
                        @foreach ($missingRequired as $jenis)
                            <li>{{ $jenis->nama }}</li>
                        @endforeach
                    </ul>
                    <div class="text-muted-soft">Pendaftaran tidak dapat dikirim sebelum seluruh dokumen wajib diunggah.</div>
                </div>
            @endif
        </div>
    </div>

    <div class="row g-3">
        @forelse ($jenisDokumenList as $jenis)
            <div class="col-12 col-md-6">
                <x-document-card
                    :jenis="$jenis"
                    :dokumen="$dokumenUploaded->get($jenis->id)"
                    :editable="$editable"
                />
            </div>
        @empty
            <div class="col-12">
                <div class="admin-card">
                    <div class="admin-card-body text-center text-muted-soft">
                        Belum ada jenis dokumen persyaratan yang tersedia.
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 mt-4">
        <a href="{{ route('calon.pendaftaran.program') }}" class="btn btn-outline-dark">
            <i class="bi bi-arrow-left me-1"></i> Sebelumnya: Pilih Program
        </a>

        @if ($documentsComplete)
            <a href="{{ route('calon.pendaftaran.review') }}" class="btn btn-primary fw-semibold">
                Lanjut ke Review <i class="bi bi-arrow-right ms-1"></i>
            </a>
        @else
            <button class="btn btn-primary fw-semibold" disabled aria-disabled="true"
                    title="Unggah seluruh dokumen wajib terlebih dahulu">
                Lanjut ke Review <i class="bi bi-lock-fill ms-1"></i>
            </button>
        @endif
    </div>
@endsection
