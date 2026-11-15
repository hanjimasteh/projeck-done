@extends('layouts.calon')

@section('title', 'Status Penerimaan')
@section('subtitle', 'Pantau status pendaftaran dan pengumuman hasil penerimaan Anda.')

@section('content')
    @if ($pendaftaran && $pendaftaran->nomor_pendaftaran)
        <div class="row g-3">
            <div class="col-12">
                <x-status-badge :status="$pendaftaran->status" />
            </div>
        </div>

        @if ($pendaftaran->status === \App\Enums\StatusPendaftaran::REJECTED && $pendaftaran->keterangan)
            <div class="alert alert-danger mt-3 mb-0">
                <strong>Catatan dari panitia:</strong> {{ $pendaftaran->keterangan }}
                <div class="mt-2">
                    <a href="{{ route('calon.pendaftaran.review') }}" class="btn btn-sm btn-danger">
                        Perbaiki Formulir
                    </a>
                </div>
            </div>
        @elseif ($pendaftaran->status === \App\Enums\StatusPendaftaran::KUOTA_PENUH)
            <div class="alert alert-warning mt-3 mb-0">
                <strong>Kuota program Anda sudah penuh.</strong>
                {{ $pendaftaran->keterangan ?? 'Silakan pilih program yang masih tersedia.' }}
                <div class="mt-2">
                    <a href="{{ route('calon.pendaftaran.program') }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-arrow-repeat me-1"></i> Ganti Program
                    </a>
                </div>
            </div>
        @endif

        @if ($pendaftaran->status !== \App\Enums\StatusPendaftaran::KUOTA_PENUH && $pendaftaran->pengumuman?->dipublikasikan)
            @if ($pendaftaran->pengumuman->hasil === \App\Models\Pengumuman::HASIL_DITERIMA)
                <div class="alert alert-success mb-3">
                    <h3 class="h6 fw-bold mb-2">
                        <i class="bi bi-check-circle-fill me-1"></i> Selamat, Anda Diterima!
                    </h3>
                    <p class="mb-0">Anda dinyatakan diterima pada PPDB SMAKNIS sesuai kuota yang tersedia.</p>
                </div>
            @else
                <div class="alert alert-danger mb-3">
                    <h3 class="h6 fw-bold mb-2">
                        <i class="bi bi-x-circle-fill me-1"></i> Belum Diterima
                    </h3>
                    <p class="mb-0">Anda belum diterima pada PPDB tahun ini.</p>
                </div>
            @endif

            @if ($pendaftaran->pengumuman->keterangan)
                <p class="mb-2"><strong>Keterangan:</strong> {{ $pendaftaran->pengumuman->keterangan }}</p>
            @endif

            @if ($pendaftaran->pengumuman->tanggal_pengumuman)
                <p class="text-muted-soft small mb-0">
                    Diumumkan {{ $pendaftaran->pengumuman->tanggal_pengumuman->translatedFormat('d F Y H:i') }}
                </p>
            @endif

            @if ($pendaftaran->seleksi?->programDiterima)
                <div class="mt-3">
                    <span class="badge text-bg-success">Diterima pada program</span>
                    <span class="ms-2">{{ $pendaftaran->seleksi->programDiterima->nama }}</span>
                </div>
            @endif
        @else
            <div class="text-center py-3">
                <i class="bi bi-hourglass-split display-6 text-muted d-block mb-2"></i>
                <p class="text-muted-soft mb-0">
                    Hasil penerimaan belum dipublikasikan. Silakan cek kembali secara berkala.
                </p>
            </div>
        @endif
    @endif
@endsection