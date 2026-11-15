@extends('layouts.calon')

@section('title', 'Hasil Penerimaan')
@section('subtitle', 'Informasi resmi penerimaan peserta didik baru PPDB SMAKNIS.')

@section('content')
    @if (!$pendaftaran || !$pendaftaran->nomor_pendaftaran)
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 text-center">
                <i class="bi bi-award display-5 text-muted d-block mb-3"></i>
                <h2 class="h5 fw-bold">Belum Ada Hasil Penerimaan</h2>
                <p class="text-muted-soft mb-4">Anda belum menyelesaikan pendaftaran PPDB.</p>
                <a href="{{ route('calon.pendaftaran.data-diri') }}" class="btn btn-primary">
                    Mulai Isi Formulir
                </a>
            </div>
        </div>
    @elseif ($pendaftaran->status === \App\Enums\StatusPendaftaran::KUOTA_PENUH)
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5 text-center">
                <div class="display-5 text-warning mb-3"><i class="bi bi-pie-chart-fill"></i></div>
                <h2 class="h3 fw-bold text-warning mb-2">Kuota Program Anda Penuh</h2>
                <p class="text-muted-soft mb-4">
                    {{ $pendaftaran->keterangan ?? 'Program yang Anda pilih sudah penuh. Silakan pilih program yang masih tersedia.' }}
                </p>
                <a href="{{ route('calon.pendaftaran.program') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-repeat me-1"></i> Ganti Program
                </a>
            </div>
        </div>
    @else
        @php($pengumuman = $pendaftaran->pengumuman)

        @if (!$pengumuman?->dipublikasikan)
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-hourglass-split display-5 text-muted d-block mb-3"></i>
                    <h2 class="h5 fw-bold">Hasil Belum Diumumkan</h2>
                    <p class="text-muted-soft mb-0">
                        Hasil penerimaan untuk nomor pendaftaran
                        <strong>{{ $pendaftaran->nomor_pendaftaran }}</strong>
                        belum dipublikasikan. Silakan cek kembali secara berkala.
                    </p>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-lg-5 text-center">
                    @if ($pengumuman->hasil === \App\Models\Pengumuman::HASIL_DITERIMA)
                        <div class="display-5 text-success mb-3"><i class="bi bi-patch-check-fill"></i></div>
                        <h2 class="h3 fw-bold text-success mb-2">Selamat, Anda Diterima!</h2>
                        <p class="text-muted-soft mb-4">
                            Berdasarkan kuota yang tersedia, Anda dinyatakan <strong>diterima</strong>.
                        </p>
                    @else
                        <div class="display-5 text-danger mb-3"><i class="bi bi-x-circle-fill"></i></div>
                        <h2 class="h3 fw-bold text-danger mb-2">Mohon Maaf, Belum Diterima</h2>
                        <p class="text-muted-soft mb-4">
                            Anda belum diterima pada PPDB tahun ini. Tetap semangat dan jangan menyerah.
                        </p>
                    @endif

                    <dl class="row justify-content-center text-start mb-4">
                        <dt class="col-sm-4 text-muted-soft fw-normal">Nomor Pendaftaran</dt>
                        <dd class="col-sm-6 fw-bold">{{ $pendaftaran->nomor_pendaftaran }}</dd>

                        <dt class="col-sm-4 text-muted-soft fw-normal">Nama Lengkap</dt>
                        <dd class="col-sm-6">{{ $siswa->nama_lengkap }}</dd>

                        <dt class="col-sm-4 text-muted-soft fw-normal">Pilihan Program 1</dt>
                        <dd class="col-sm-6">{{ $pendaftaran->programPilihan1?->nama ?? '-' }}</dd>

                        <dt class="col-sm-4 text-muted-soft fw-normal">Program Diterima</dt>
                        <dd class="col-sm-6">
                            {{ $pendaftaran->seleksi?->programDiterima?->nama ?? '-' }}
                        </dd>

                        @if ($pengumuman->keterangan)
                            <dt class="col-sm-4 text-muted-soft fw-normal">Keterangan</dt>
                            <dd class="col-sm-6 mb-0">{{ $pengumuman->keterangan }}</dd>
                        @endif
                    </dl>

                    @if ($pengumuman->hasil === \App\Models\Pengumuman::HASIL_DITERIMA)
                        <a href="{{ route('calon.daftar-ulang') }}" class="btn btn-primary">
                            <i class="bi bi-journal-check me-1"></i> Lanjut ke Daftar Ulang
                        </a>
                    @endif
                </div>
            </div>
        @endif
    @endif
@endsection