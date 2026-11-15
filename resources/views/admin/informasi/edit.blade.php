@extends('layouts.backend')

@section('title', 'Edit Informasi')
@section('subtitle', 'Perbarui informasi publik yang tampil di beranda website.')

@section('actions')
    <a href="{{ route('admin.informasi.index') }}" class="btn btn-outline-dark">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
@endsection

@section('content')
    <div class="admin-card">
        <div class="admin-card-body">
            <form action="{{ route('admin.informasi.update', $informasi->id) }}" method="POST" class="d-grid gap-3">
                @csrf
                @method('PUT')

                <div>
                    <label for="judul" class="form-label fw-semibold">Judul Informasi</label>
                    <input type="text" id="judul" name="judul" value="{{ old('judul', $informasi->judul) }}" maxlength="255"
                           class="form-control @error('judul') is-invalid @enderror" required>
                    @error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label for="isi_konten" class="form-label fw-semibold">Isi Konten</label>
                    <textarea id="isi_konten" name="isi_konten" rows="6"
                              class="form-control @error('isi_konten') is-invalid @enderror" required>{{ old('isi_konten', $informasi->isi_konten) }}</textarea>
                    @error('isi_konten')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="status" class="form-label fw-semibold">Status</label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="draft" @selected(old('status', $informasi->status) === 'draft')>Draf</option>
                            <option value="terbit" @selected(old('status', $informasi->status) === 'terbit')>Terbit</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="tanggal" class="form-label fw-semibold">Tanggal</label>
                        <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', $informasi->tanggal?->format('Y-m-d')) }}"
                               class="form-control @error('tanggal') is-invalid @enderror">
                        @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.informasi.index') }}" class="btn btn-outline-dark">Batal</a>
                    <button type="submit" class="btn btn-primary fw-semibold">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
