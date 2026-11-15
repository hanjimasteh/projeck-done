@extends('layouts.backend')

@section('title', 'Tambah Informasi')
@section('subtitle', 'Buat informasi publik yang akan tampil di beranda website.')

@section('actions')
    <a href="{{ route('admin.informasi.index') }}" class="btn btn-outline-dark">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
@endsection

@section('content')
    <div class="admin-card">
        <div class="admin-card-body">
            <form action="{{ route('admin.informasi.store') }}" method="POST" class="d-grid gap-3">
                @csrf

                <div>
                    <label for="judul" class="form-label fw-semibold">Judul Informasi</label>
                    <input type="text" id="judul" name="judul" value="{{ old('judul') }}" maxlength="255"
                           class="form-control @error('judul') is-invalid @enderror"
                           placeholder="Contoh: Pengumuman Hasil Seleksi Gelombang 1" required>
                    @error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label for="isi_konten" class="form-label fw-semibold">Isi Konten</label>
                    <textarea id="isi_konten" name="isi_konten" rows="6"
                              class="form-control @error('isi_konten') is-invalid @enderror"
                              placeholder="Tuliskan informasi yang ingin ditampilkan ke calon pendaftar." required>{{ old('isi_konten') }}</textarea>
                    @error('isi_konten')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="status" class="form-label fw-semibold">Status</label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="draft" @selected(old('status') === 'draft')>Draf</option>
                            <option value="terbit" @selected(old('status') === 'terbit')>Terbit</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="tanggal" class="form-label fw-semibold">Tanggal</label>
                        <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal') }}"
                               class="form-control @error('tanggal') is-invalid @enderror">
                        @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.informasi.index') }}" class="btn btn-outline-dark">Batal</a>
                    <button type="submit" class="btn btn-primary fw-semibold">
                        <i class="bi bi-check-lg me-1"></i> Simpan Informasi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
