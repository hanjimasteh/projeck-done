@extends('layouts.backend')

@section('title', 'Edit Program')
@section('subtitle', 'Perbarui kode, nama, dan kuota awal program keahlian.')

@section('actions')
    <a href="{{ route('admin.program.index') }}" class="btn btn-outline-dark">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
@endsection

@section('content')
    <div class="admin-card">
        <div class="admin-card-body">
            <form action="{{ route('admin.program.update', $program->id) }}" method="POST" class="d-grid gap-3">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label for="kode" class="form-label fw-semibold">Kode Program</label>
                        <input type="text" id="kode" name="kode" value="{{ old('kode', $program->kode) }}" maxlength="50"
                               class="form-control @error('kode') is-invalid @enderror" required>
                        @error('kode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-8">
                        <label for="nama" class="form-label fw-semibold">Nama Program / Jurusan</label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama', $program->nama) }}" maxlength="255"
                               class="form-control @error('nama') is-invalid @enderror" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label for="kuota" class="form-label fw-semibold">Kuota Penerimaan</label>
                        <input type="number" id="kuota" name="kuota" value="{{ old('kuota', $program->kuota) }}" min="1"
                               class="form-control @error('kuota') is-invalid @enderror" required>
                        @error('kuota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Kuota yang sudah terisi tidak boleh dikurangi.</div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.program.index') }}" class="btn btn-outline-dark">Batal</a>
                    <button type="submit" class="btn btn-primary fw-semibold">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
