@extends('layouts.backend')

@section('title', 'Verifikasi Berkas')
@section('subtitle', 'Periksa data dan dokumen, lalu tentukan status penerimaan berdasarkan kuota.')

@section('actions')
    <a href="{{ route('admin.verifikasi') }}" class="btn btn-outline-dark">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
@endsection

@section('content')
    @php
        $orangTua = $pendaftaran->calonSiswa?->orangTua?->keyBy('hubungan');
        $jenisDokumen = \App\Models\JenisDokumen::orderBy('kategori')->orderBy('nama')->get();
        $terunggah = $pendaftaran->dokumen->keyBy('jenis_dokumen_id');
    @endphp

    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="admin-card mb-3">
                <div class="admin-card-header">
                    <h2 class="h6 fw-bold mb-0">Data Pendaftar</h2>
                    <x-status-badge :status="$pendaftaran->status" />
                </div>
                <div class="admin-card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted-soft fw-normal">No. Pendaftaran</dt>
                        <dd class="col-sm-8 fw-bold">{{ $pendaftaran->nomor_pendaftaran ?? 'Belum diterbitkan' }}</dd>

                        <dt class="col-sm-4 text-muted-soft fw-normal">Nama Lengkap</dt>
                        <dd class="col-sm-8">{{ $pendaftaran->calonSiswa?->nama_lengkap ?? '-' }}</dd>

                        <dt class="col-sm-4 text-muted-soft fw-normal">NIK / NISN</dt>
                        <dd class="col-sm-8">{{ $pendaftaran->calonSiswa?->nik ?? '-' }} / {{ $pendaftaran->calonSiswa?->nisn ?? '-' }}</dd>

                        <dt class="col-sm-4 text-muted-soft fw-normal">Tempat, Tanggal Lahir</dt>
                        <dd class="col-sm-8">
                            {{ $pendaftaran->calonSiswa?->tempat_lahir ?? '-' }},
                            {{ $pendaftaran->calonSiswa?->tanggal_lahir?->translatedFormat('d F Y') ?? '-' }}
                        </dd>

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
                        <dd class="col-sm-8 fw-semibold text-primary mb-0">
                            {{ $pendaftaran->seleksi?->programDiterima?->nama ?? 'Belum dialokasikan' }}
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="admin-card mb-3">
                <div class="admin-card-header">
                    <h2 class="h6 fw-bold mb-0">Data Orang Tua / Wali</h2>
                </div>
                <div class="admin-card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted-soft fw-normal">Nama Ayah</dt>
                        <dd class="col-sm-8">{{ $orangTua['ayah']->nama ?? '-' }}</dd>

                        <dt class="col-sm-4 text-muted-soft fw-normal">Pekerjaan Ayah</dt>
                        <dd class="col-sm-8">{{ $orangTua['ayah']->pekerjaan ?? '-' }}</dd>

                        <dt class="col-sm-4 text-muted-soft fw-normal">Nama Ibu</dt>
                        <dd class="col-sm-8">{{ $orangTua['ibu']->nama ?? '-' }}</dd>

                        <dt class="col-sm-4 text-muted-soft fw-normal">Pekerjaan Ibu</dt>
                        <dd class="col-sm-8 mb-0">{{ $orangTua['ibu']->pekerjaan ?? '-' }}</dd>
                    </dl>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <h2 class="h6 fw-bold mb-0">Dokumen Persyaratan</h2>
                    <span class="text-muted-soft small">{{ $pendaftaran->dokumen->count() }} dari {{ $jenisDokumen->count() }} berkas</span>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nama Dokumen</th>
                                <th style="width: 180px;">Status Berkas</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jenisDokumen as $jenis)
                                @php($dokumen = $terunggah->get($jenis->id))
                                <tr>
                                    <td class="fw-semibold">{{ $jenis->nama }}</td>
                                    <td>
                                        @if ($dokumen)
                                            <span class="badge {{ $dokumen->status === \App\Enums\StatusDokumen::APPROVED ? 'text-bg-success' : ($dokumen->status === \App\Enums\StatusDokumen::REJECTED ? 'text-bg-danger' : 'text-bg-secondary') }}">
                                                {{ $dokumen->status === \App\Enums\StatusDokumen::APPROVED ? 'Disetujui' : ($dokumen->status === \App\Enums\StatusDokumen::REJECTED ? 'Ditolak' : 'Menunggu') }}
                                            </span>
                                        @else
                                            <span class="badge text-bg-danger">Belum Diunggah</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($dokumen)
                                            <a href="{{ route('admin.dokumen.preview', $dokumen->id) }}" target="_blank" class="btn btn-sm btn-outline-primary fw-semibold">
                                                <i class="bi bi-eye me-1"></i> Lihat
                                            </a>
                                        @else
                                            <span class="text-muted-soft small">Tidak ada file</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2 class="h6 fw-bold mb-0">Status Verifikasi</h2>
                </div>
                <div class="admin-card-body">
                    <div class="alert alert-info border-0 shadow-sm small">
                        <i class="bi bi-info-circle me-1"></i>
                        Memilih <strong>Terverifikasi</strong> akan otomatis mengecek kuota.
                        Bila kursi tersedia, pendaftar langsung <strong>Diterima</strong>.
                        Bila kuota program penuh, pendaftar diminta memilih program lain.
                    </div>

                    <form action="{{ route('admin.verifikasi.update', $pendaftaran->id) }}" method="POST" class="d-grid gap-3">
                        @csrf

                        <div>
                            <label for="status" class="form-label fw-semibold">Ubah Status</label>
                            <select id="status" name="status" class="form-select" required>
                                @foreach (\App\Enums\StatusPendaftaran::cases() as $item)
                                    <option value="{{ $item->value }}" @selected($pendaftaran->status === $item)>{{ $item->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="keterangan" class="form-label fw-semibold">Catatan / Keterangan</label>
                            <textarea id="keterangan" name="keterangan" rows="3" class="form-control"
                                      placeholder="Contoh: Berkas lengkap, atau catatan revisi.">{{ old('keterangan', $pendaftaran->keterangan) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary fw-semibold">
                            <i class="bi bi-check-lg me-1"></i> Simpan Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
