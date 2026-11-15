@extends('layouts.backend')

@section('title', 'Edit Tahap')
@section('subtitle', 'Perbarui tahapan dan tanggal pada jadwal PPDB.')

@section('actions')
    <a href="{{ route('admin.jadwal.index') }}" class="btn btn-outline-dark">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
@endsection

@section('content')
    <div class="admin-card">
        <div class="admin-card-body">
            <form action="{{ route('admin.jadwal.update', $jadwal->id) }}" method="POST" class="d-grid gap-3">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <label for="tahapan" class="form-label fw-semibold">Nama Tahapan</label>
                        <input type="text" id="tahapan" name="tahapan" value="{{ old('tahapan', $jadwal->tahapan) }}" maxlength="100"
                               class="form-control @error('tahapan') is-invalid @enderror" required>
                        @error('tahapan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="urutan" class="form-label fw-semibold">Urutan</label>
                        <input type="number" id="urutan" name="urutan" value="{{ old('urutan', $jadwal->urutan) }}" min="1"
                               class="form-control @error('urutan') is-invalid @enderror" required>
                        @error('urutan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="tanggal_mulai" class="form-label fw-semibold">Tanggal Mulai</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', $jadwal->tanggal_mulai->format('Y-m-d')) }}"
                               class="form-control @error('tanggal_mulai') is-invalid @enderror" required>
                        @error('tanggal_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="tanggal_selesai" class="form-label fw-semibold">Tanggal Selesai</label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', $jadwal->tanggal_selesai->format('Y-m-d')) }}"
                               class="form-control @error('tanggal_selesai') is-invalid @enderror" required>
                        @error('tanggal_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="ppdb_id" class="form-label fw-semibold">Periode PPDB</label>
                        <select id="ppdb_id" name="ppdb_id" class="form-select @error('ppdb_id') is-invalid @enderror" required>
                            <option value="">-- Pilih periode --</option>
                            @foreach (\App\Models\Ppdb::orderByDesc('tanggal_mulai')->get() as $item)
                                <option value="{{ $item->id }}" @selected((string) old('ppdb_id', $jadwal->ppdb_id) === (string) $item->id)>
                                    TA {{ $item->tahun_ajaran }}
                                </option>
                            @endforeach
                        </select>
                        @error('ppdb_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.jadwal.index') }}" class="btn btn-outline-dark">Batal</a>
                    <button type="submit" class="btn btn-primary fw-semibold">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
