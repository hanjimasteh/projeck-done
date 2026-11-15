@extends('layouts.calon')

@section('title', 'Formulir Pendaftaran')
@section('subtitle', 'Langkah 1 dari 5 - Data diri')

@section('content')
    @include('calon.pendaftaran._stepper')

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

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                <div>
                    <span class="section-eyebrow">Langkah 1</span>
                    <h2 class="h4 fw-bold mt-1 mb-1">Data Diri Calon Siswa</h2>
                    <p class="text-muted-soft mb-0">Isi data sesuai dokumen resmi. Data tersimpan sebagai draft.</p>
                </div>
                <span class="info-icon d-none d-sm-inline-flex"><i class="bi bi-person-vcard"></i></span>
            </div>

            <form action="{{ route('calon.pendaftaran.data-diri.store') }}" method="POST" enctype="multipart/form-data" class="d-grid gap-4">
                @csrf

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="nik" class="form-label fw-semibold">NIK <span class="text-danger">*</span></label>
                        <input type="text" inputmode="numeric" id="nik" name="nik" maxlength="16"
                               value="{{ old('nik', $siswa->nik) }}"
                               class="form-control @error('nik') is-invalid @enderror"
                               placeholder="16 digit angka" @disabled(! $editable)>
                        @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="nisn" class="form-label fw-semibold">NISN <span class="text-danger">*</span></label>
                        <input type="text" inputmode="numeric" id="nisn" name="nisn" maxlength="10"
                               value="{{ old('nisn', $siswa->nisn) }}"
                               class="form-control @error('nisn') is-invalid @enderror"
                               placeholder="10 digit angka" @disabled(! $editable)>
                        @error('nisn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label for="nama_lengkap" class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap"
                               value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}"
                               class="form-control @error('nama_lengkap') is-invalid @enderror"
                               placeholder="Nama sesuai akta kelahiran" @disabled(! $editable)>
                        @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="tempat_lahir" class="form-label fw-semibold">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" id="tempat_lahir" name="tempat_lahir"
                               value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}"
                               class="form-control @error('tempat_lahir') is-invalid @enderror"
                               @disabled(! $editable)>
                        @error('tempat_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="tanggal_lahir" class="form-label fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                               value="{{ old('tanggal_lahir', $siswa->tanggal_lahir?->format('Y-m-d')) }}"
                               class="form-control @error('tanggal_lahir') is-invalid @enderror"
                               @disabled(! $editable)>
                        @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="jenis_kelamin" class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select id="jenis_kelamin" name="jenis_kelamin"
                                class="form-select @error('jenis_kelamin') is-invalid @enderror"
                                @disabled(! $editable)>
                            <option value="">-- Pilih jenis kelamin --</option>
                            <option value="L" @selected(old('jenis_kelamin', $siswa->jenis_kelamin) === 'L')>Laki-laki</option>
                            <option value="P" @selected(old('jenis_kelamin', $siswa->jenis_kelamin) === 'P')>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="no_hp" class="form-label fw-semibold">No. HP / WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" id="no_hp" name="no_hp" maxlength="20"
                               value="{{ old('no_hp', $siswa->no_hp) }}"
                               class="form-control @error('no_hp') is-invalid @enderror"
                               placeholder="0812xxxxxxx" @disabled(! $editable)>
                        @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label for="alamat" class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea id="alamat" name="alamat" rows="3"
                                  class="form-control @error('alamat') is-invalid @enderror"
                                  @disabled(! $editable)>{{ old('alamat', $siswa->alamat) }}</textarea>
                        @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="asal_sekolah" class="form-label fw-semibold">Asal Sekolah <span class="text-danger">*</span></label>
                        <input type="text" id="asal_sekolah" name="asal_sekolah"
                               value="{{ old('asal_sekolah', $siswa->asal_sekolah) }}"
                               class="form-control @error('asal_sekolah') is-invalid @enderror"
                               placeholder="SMP/MTs asal" @disabled(! $editable)>
                        @error('asal_sekolah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="foto" class="form-label fw-semibold">Foto Calon Siswa</label>
                        <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png"
                               class="form-control @error('foto') is-invalid @enderror"
                               @disabled(! $editable)>
                        <div class="form-text">Format JPG/JPEG/PNG, maksimal 2 MB. Foto disimpan secara privat.</div>
                        @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if ($siswa->foto)
                            <div class="small text-success mt-1"><i class="bi bi-check2-circle me-1"></i>Foto sudah tersimpan.</div>
                        @endif
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-between gap-2">
                    <a href="{{ route('calon.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                    </a>

                    @if ($editable)
                        <button type="submit" class="btn btn-primary fw-semibold">
                            Simpan &amp; Lanjut <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
