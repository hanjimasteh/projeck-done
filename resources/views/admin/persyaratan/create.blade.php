@extends('layouts.backend')

@section('title', 'Tambah Persyaratan')
@section('subtitle', 'Tambahkan dokumen persyaratan baru.')

@section('actions')
    <a href="{{ route('admin.persyaratan.index') }}" class="btn btn-outline-dark">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
@endsection

@section('content')
    <div class="admin-card">
        <div class="admin-card-body">
            <form action="{{ route('admin.persyaratan.store') }}" method="POST" class="d-grid gap-3">
                @csrf

                <div>
                    <label for="nama" class="form-label fw-semibold">Nama Persyaratan</label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}" maxlength="255"
                           class="form-control @error('nama') is-invalid @enderror"
                           placeholder="Contoh: Kartu Keluarga" required>
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3"
                              class="form-control @error('deskripsi') is-invalid @enderror"
                              placeholder="Penjelasan singkat mengenai dokumen ini.">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="wajib" name="wajib" @checked(old('wajib', true))>
                            <label class="form-check-label fw-semibold" for="wajib">Wajib diunggah</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.persyaratan.index') }}" class="btn btn-outline-dark">Batal</a>
                    <button type="submit" class="btn btn-primary fw-semibold">
                        <i class="bi bi-check-lg me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
