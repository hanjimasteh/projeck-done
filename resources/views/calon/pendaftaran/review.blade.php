@extends('layouts.calon')

@section('title', 'Formulir Pendaftaran')
@section('subtitle', 'Langkah 5 dari 5 - Review & submit')

@section('content')
    @include('calon.pendaftaran._stepper')

    @if ($pendaftaran?->status === \App\Enums\StatusPendaftaran::REJECTED)
        <div class="alert alert-danger border-0 shadow-sm">
            <div class="d-flex gap-2">
                <i class="bi bi-exclamation-octagon-fill mt-1"></i>
                <div>
                    <strong>Pendaftaran perlu diperbaiki.</strong>
                    @if ($pendaftaran->keterangan)
                        <div class="mt-1">Catatan revisi: {{ $pendaftaran->keterangan }}</div>
                    @endif
                    <div class="small mt-1">Perbaiki data yang diperlukan, lalu kirim ulang pendaftaran.</div>
                </div>
            </div>
        </div>
    @endif

    @if ($locked)
        <div class="alert alert-info d-flex gap-2 border-0 shadow-sm">
            <i class="bi bi-lock-fill mt-1"></i>
            <div>Formulir sudah dikirim dan terkunci. Data hanya dapat dilihat.</div>
        </div>
    @elseif (! $editable)
        <div class="alert alert-warning d-flex gap-2 border-0 shadow-sm">
            <i class="bi bi-clock-fill mt-1"></i>
            <div>{{ $closedMessage }}</div>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
                        <div>
                            <span class="section-eyebrow">Langkah 5</span>
                            <h2 class="h4 fw-bold mt-1 mb-0">Review Data Pendaftaran</h2>
                        </div>
                        @if ($pendaftaran)
                            <x-status-badge :status="$pendaftaran->status" />
                        @endif
                    </div>

                    @if ($pendaftaran?->nomor_pendaftaran)
                        <div class="alert alert-success border-0">
                            Nomor pendaftaran: <strong>{{ $pendaftaran->nomor_pendaftaran }}</strong>
                        </div>
                    @endif

                    @unless ($complete)
                        <div class="alert alert-warning border-0">
                            Masih ada data yang belum lengkap.
                            <a href="{{ route($nextRoute) }}" class="alert-link">Lengkapi sekarang</a>.
                        </div>
                    @endunless

                    @unless ($documentsComplete)
                        <div class="alert alert-warning border-0">
                            <div class="fw-semibold mb-1">Dokumen wajib belum lengkap.</div>
                            <ul class="mb-1 ps-3">
                                @foreach ($missingRequired as $jenis)
                                    <li>{{ $jenis->nama }}</li>
                                @endforeach
                            </ul>
                            <a href="{{ route('calon.pendaftaran.dokumen') }}" class="alert-link">
                                Lengkapi dokumen sekarang
                            </a>.
                        </div>
                    @endunless

                    <h3 class="h6 fw-bold text-uppercase text-muted-soft mb-3">Data Diri</h3>
                    <dl class="row small mb-4">
                        <dt class="col-5 col-sm-4">NIK</dt><dd class="col-7 col-sm-8">{{ $siswa->nik ?: '-' }}</dd>
                        <dt class="col-5 col-sm-4">NISN</dt><dd class="col-7 col-sm-8">{{ $siswa->nisn ?: '-' }}</dd>
                        <dt class="col-5 col-sm-4">Nama Lengkap</dt><dd class="col-7 col-sm-8">{{ $siswa->nama_lengkap ?: '-' }}</dd>
                        <dt class="col-5 col-sm-4">Tempat, Tanggal Lahir</dt>
                        <dd class="col-7 col-sm-8">
                            {{ $siswa->tempat_lahir ?: '-' }}, {{ $siswa->tanggal_lahir?->format('d/m/Y') ?: '-' }}
                        </dd>
                        <dt class="col-5 col-sm-4">Jenis Kelamin</dt>
                        <dd class="col-7 col-sm-8">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</dd>
                        <dt class="col-5 col-sm-4">Alamat</dt><dd class="col-7 col-sm-8">{{ $siswa->alamat ?: '-' }}</dd>
                        <dt class="col-5 col-sm-4">No. HP</dt><dd class="col-7 col-sm-8">{{ $siswa->no_hp ?: '-' }}</dd>
                        <dt class="col-5 col-sm-4">Asal Sekolah</dt><dd class="col-7 col-sm-8">{{ $siswa->asal_sekolah ?: '-' }}</dd>
                        <dt class="col-5 col-sm-4">Foto</dt>
                        <dd class="col-7 col-sm-8">{{ $siswa->foto ? 'Sudah diunggah' : 'Belum diunggah' }}</dd>
                    </dl>

                    <h3 class="h6 fw-bold text-uppercase text-muted-soft mb-3">Orang Tua / Wali</h3>
                    <dl class="row small mb-4">
                        @foreach (['ayah' => 'Ayah', 'ibu' => 'Ibu', 'wali' => 'Wali'] as $key => $label)
                            @php($row = $siswa->orangTua->firstWhere('hubungan', $key))
                            <dt class="col-5 col-sm-4">{{ $label }}</dt>
                            <dd class="col-7 col-sm-8">
                                {{ $row?->nama ?: '-' }}
                                @if ($row?->pekerjaan)
                                    <span class="text-muted-soft">({{ $row->pekerjaan }})</span>
                                @endif
                            </dd>
                        @endforeach
                    </dl>

                    <h3 class="h6 fw-bold text-uppercase text-muted-soft mb-3">Program Pilihan</h3>
                    <dl class="row small mb-4">
                        <dt class="col-5 col-sm-4">Periode</dt><dd class="col-7 col-sm-8">{{ $pendaftaran?->ppdb?->tahun_ajaran ?? '-' }}</dd>
                        <dt class="col-5 col-sm-4">Pilihan 1</dt>
                        <dd class="col-7 col-sm-8">{{ $pendaftaran?->programPilihan1?->nama ?: '-' }}</dd>
                        <dt class="col-5 col-sm-4">Pilihan 2</dt>
                        <dd class="col-7 col-sm-8">{{ $pendaftaran?->programPilihan2?->nama ?: '-' }}</dd>
                    </dl>

                    <h3 class="h6 fw-bold text-uppercase text-muted-soft mb-3">Dokumen Persyaratan</h3>
                    <div class="list-group list-group-flush small">
                        @forelse ($jenisDokumenList as $jenis)
                            @php($dokumen = $dokumenUploaded->get($jenis->id))
                            <div class="list-group-item px-0 d-flex align-items-start justify-content-between gap-3">
                                <div class="min-w-0">
                                    <div class="fw-semibold">
                                        {{ $jenis->nama }}
                                        <span class="badge {{ $jenis->wajib ? 'text-bg-danger' : 'text-bg-secondary' }} ms-1">
                                            {{ $jenis->wajib ? 'Wajib' : 'Opsional' }}
                                        </span>
                                    </div>

                                    @if ($dokumen)
                                        <x-status-badge :status="$dokumen->status" class="mt-1" />
                                        @if ($dokumen->keterangan)
                                            <div class="text-danger mt-1">Catatan: {{ $dokumen->keterangan }}</div>
                                        @endif
                                    @else
                                        <span class="badge text-bg-secondary mt-1">Belum diunggah</span>
                                    @endif
                                </div>

                                @if ($dokumen)
                                    <a href="{{ route('calon.dokumen.preview', $dokumen) }}"
                                       target="_blank"
                                       rel="noopener"
                                       class="btn btn-sm btn-outline-primary flex-shrink-0">
                                        <i class="bi bi-eye me-1"></i> Lihat
                                    </a>
                                @endif
                            </div>
                        @empty
                            <div class="list-group-item px-0 text-muted-soft">
                                Belum ada jenis dokumen persyaratan yang tersedia.
                            </div>
                        @endforelse
                    </div>

                    <a href="{{ route('calon.pendaftaran.dokumen') }}" class="btn btn-outline-secondary btn-sm mt-3">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Kelola Dokumen
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 sticky-lg-top" style="top: 1.5rem;">
                <div class="card-body p-4">
                    <h3 class="h6 fw-bold mb-3">Kirim Pendaftaran</h3>

                    <p class="small text-muted-soft">
                        Pastikan seluruh data sudah benar. Setelah dikirim, formulir terkunci dan tidak dapat
                        diubah kecuali pendaftaran dikembalikan untuk revisi.
                    </p>

                    @if ($editable && $canSubmit)
                        <form action="{{ route('calon.pendaftaran.submit') }}" method="POST" class="d-grid gap-3">
                            @csrf
                            <div class="form-check">
                                <input class="form-check-input @error('pernyataan') is-invalid @enderror"
                                       type="checkbox" name="pernyataan" id="pernyataan" value="1"
                                       @checked(old('pernyataan'))>
                                <label class="form-check-label small" for="pernyataan">
                                    Saya menyatakan seluruh data yang diisi benar dan dapat dipertanggungjawabkan.
                                </label>
                                @error('pernyataan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <button type="submit" class="btn btn-primary fw-semibold">
                                <i class="bi bi-send-check me-1"></i>
                                {{ $pendaftaran?->status === \App\Enums\StatusPendaftaran::REJECTED ? 'Kirim Ulang Pendaftaran' : 'Submit Pendaftaran' }}
                            </button>
                        </form>
                    @elseif ($pendaftaran?->status === \App\Enums\StatusPendaftaran::SUBMITTED)
                        <div class="alert alert-info border-0 small mb-0">
                            Pendaftaran sudah dikirim. Silakan pantau status verifikasi secara berkala.
                        </div>
                    @elseif (! $complete)
                        <a href="{{ route($nextRoute) }}" class="btn btn-warning w-100 fw-semibold">
                            Lengkapi Formulir
                        </a>
                    @elseif (! $documentsComplete)
                        <div class="alert alert-warning border-0 small">
                            Dokumen wajib belum lengkap. Unggah seluruh dokumen wajib sebelum mengirim pendaftaran.
                        </div>
                        <a href="{{ route('calon.pendaftaran.dokumen') }}" class="btn btn-warning w-100 fw-semibold">
                            Lengkapi Dokumen Wajib
                        </a>
                    @else
                        <div class="alert alert-warning border-0 small mb-0">
                            {{ $closedMessage }}
                        </div>
                    @endif

                    <hr>

                    <a href="{{ route('calon.pendaftaran.dokumen') }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dokumen
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
