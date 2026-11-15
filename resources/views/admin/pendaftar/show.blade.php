@extends('layouts.backend')

@section('title', 'Detail Pendaftar')
@section('subtitle', 'Data lengkap, pilihan program, dan hasil alokasi kuota pendaftar.')

@section('actions')
    <a href="{{ route('admin.verifikasi.show', $pendaftaran->id) }}" class="btn btn-outline-dark">
        <i class="bi bi-file-earmark-check me-1"></i> Verifikasi Berkas
    </a>
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="admin-card h-100">
                <div class="admin-card-header">
                    <h2 class="h6 fw-bold mb-0">Data Pendaftar</h2>
                    <x-status-badge :status="$pendaftaran->status" />
                </div>
                <div class="admin-card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted-soft fw-normal">Nomor Pendaftaran</dt>
                        <dd class="col-sm-8 fw-bold">{{ $pendaftaran->nomor_pendaftaran ?? 'Belum diterbitkan' }}</dd>

                        <dt class="col-sm-4 text-muted-soft fw-normal">Nama Lengkap</dt>
                        <dd class="col-sm-8">{{ $pendaftaran->calonSiswa?->nama_lengkap ?? '-' }}</dd>

                        <dt class="col-sm-4 text-muted-soft fw-normal">NISN / NIK</dt>
                        <dd class="col-sm-8">{{ $pendaftaran->calonSiswa?->nisn ?? '-' }} / {{ $pendaftaran->calonSiswa?->nik ?? '-' }}</dd>

                        <dt class="col-sm-4 text-muted-soft fw-normal">Asal Sekolah</dt>
                        <dd class="col-sm-8">{{ $pendaftaran->calonSiswa?->asal_sekolah ?? '-' }}</dd>

                        <dt class="col-sm-4 text-muted-soft fw-normal">Pilihan Program 1</dt>
                        <dd class="col-sm-8">
                            {{ $pendaftaran->programPilihan1?->nama ?? '-' }}
                            @if ($pendaftaran->programPilihan1)
                                <span class="text-muted-soft">({{ $pendaftaran->programPilihan1->kode }})</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4 text-muted-soft fw-normal">Pilihan 2</dt>
                        <dd class="col-sm-8">
                            {{ $pendaftaran->programPilihan2?->nama ?? '-' }}
                            @if ($pendaftaran->programPilihan2)
                                <span class="text-muted-soft">({{ $pendaftaran->programPilihan2->kode }})</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4 text-muted-soft fw-normal">Program Diterima</dt>
                        <dd class="col-sm-8 fw-semibold text-primary">
                            {{ $pendaftaran->seleksi?->programDiterima?->nama ?? 'Belum dialokasikan' }}
                        </dd>

                        <dt class="col-sm-4 text-muted-soft fw-normal">Tanggal Daftar</dt>
                        <dd class="col-sm-8 mb-0">{{ $pendaftaran->tanggal_pendaftaran?->translatedFormat('d F Y H:i') ?? '-' }}</dd>
                    </dl>

                    @if ($pendaftaran->keterangan)
                        <div class="alert alert-info border-0 shadow-sm mt-3 mb-0 small">
                            <strong>Catatan:</strong> {{ $pendaftaran->keterangan }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="admin-card mb-3">
                <div class="admin-card-header">
                    <h2 class="h6 fw-bold mb-0">Dokumen Berkas</h2>
                    <span class="text-muted-soft small">{{ $pendaftaran->dokumen->count() }} berkas</span>
                </div>
                <div class="admin-card-body">
                    @forelse ($pendaftaran->dokumen as $dokumen)
                        <div class="d-flex justify-content-between align-items-center gap-2 py-2 border-bottom">
                            <div class="min-w-0">
                                <div class="fw-semibold text-truncate">{{ $dokumen->jenisDokumen?->nama ?? 'Dokumen' }}</div>
                                <small class="text-muted-soft">{{ basename($dokumen->file) }}</small>
                            </div>
                            <x-status-badge :status="$dokumen->status" />
                        </div>
                    @empty
                        <div class="admin-empty py-4">Belum ada dokumen yang diunggah.</div>
                    @endforelse
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <h2 class="h6 fw-bold mb-0">Hasil Penerimaan</h2>
                </div>
                <div class="admin-card-body">
                    @if ($pendaftaran->pengumuman?->dipublikasikan)
                        <span class="badge text-bg-{{ $pendaftaran->pengumuman->hasil === \App\Models\Pengumuman::HASIL_DITERIMA ? 'success' : 'danger' }} mb-2">
                            {{ $pendaftaran->pengumuman->hasil === \App\Models\Pengumuman::HASIL_DITERIMA ? 'Diterima' : 'Tidak Diterima' }}
                        </span>
                        <p class="small text-muted-soft mb-1">{{ $pendaftaran->pengumuman->keterangan }}</p>
                        <small class="text-muted-soft">
                            Diumumkan {{ $pendaftaran->pengumuman->tanggal_pengumuman?->translatedFormat('d F Y H:i') }}
                        </small>
                    @else
                        <div class="admin-empty py-3">
                            Belum ada hasil penerimaan untuk pendaftar ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
