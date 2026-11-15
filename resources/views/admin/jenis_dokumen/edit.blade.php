@extends('layouts.backend')

@section('title', 'Edit Jenis Dokumen')
@section('subtitle', 'Perbarui nama, format, dan batas ukuran berkas.')

@section('actions')
    <a href="{{ route('admin.jenis-dokumen.index') }}" class="btn btn-outline-dark">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
@endsection

@section('content')
    <div class="admin-card">
        <div class="admin-card-body">
            <form action="{{ route('admin.jenis-dokumen.update', $jenisDokumen->id) }}" method="POST" class="d-grid gap-3">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12 col-md-7">
                        <label for="nama" class="form-label fw-semibold">Nama Dokumen</label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama', $jenisDokumen->nama) }}" maxlength="255"
                               class="form-control @error('nama') is-invalid @enderror" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-5">
                        <label for="kategori" class="form-label fw-semibold">Kategori</label>
                        <input type="text" id="kategori" name="kategori" value="{{ old('kategori', $jenisDokumen->kategori) }}" maxlength="100"
                               class="form-control @error('kategori') is-invalid @enderror" required>
                        @error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="format_file" class="form-label fw-semibold">Format File</label>
                        <input type="text" id="format_file" name="format_file" value="{{ old('format_file', $jenisDokumen->format_file) }}" maxlength="255"
                               class="form-control @error('format_file') is-invalid @enderror" required>
                        @error('format_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="ukuran_maks_kb" class="form-label fw-semibold">Maks. Ukuran (KB)</label>
                        <input type="number" id="ukuran_maks_kb" name="ukuran_maks_kb" value="{{ old('ukuran_maks_kb', $jenisDokumen->ukuran_maks_kb) }}" min="1"
                               class="form-control @error('ukuran_maks_kb') is-invalid @enderror" required>
                        @error('ukuran_maks_kb')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-3 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="wajib" name="wajib" @checked(old('wajib', $jenisDokumen->wajib))>
                            <label class="form-check-label fw-semibold" for="wajib">Wajib diunggah</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.jenis-dokumen.index') }}" class="btn btn-outline-dark">Batal</a>
                    <button type="submit" class="btn btn-primary fw-semibold">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
