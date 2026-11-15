@extends('layouts.calon')

@section('title', 'Daftar Ulang')
@section('subtitle', 'Lengkapi data daftar ulang Anda sebagai pendaftar yang diterima.')

@section('content')
    @php($daftarUlang = optional($pendaftaran->daftarUlang))
    @php($program = $pendaftaran->seleksi?->programDiterima ?? $pendaftaran->programPilihan1)

    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex gap-2">
            <i class="bi bi-check-circle-fill mt-1"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger border-0 shadow-sm d-flex gap-2">
            <i class="bi bi-exclamation-octagon-fill mt-1"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="admin-stat h-100">
                <div class="stat-value text-primary">{{ $pendaftaran->nomor_pendaftaran }}</div>
                <div class="stat-label">Nomor Pendaftaran</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat h-100">
                <div class="stat-value">{{ $program?->kode ?? '-' }}</div>
                <div class="stat-label">Program Diterima</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat h-100">
                <div class="stat-value">{{ $daftarUlang->status ? strtoupper($daftarUlang->status) : 'BELUM' }}</div>
                <div class="stat-label">Status Daftar Ulang</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="admin-stat h-100">
                <div class="stat-value">{{ $daftarUlang->tanggal_daftar_ulang?->translatedFormat('d M Y') ?? '-' }}</div>
                <div class="stat-label">Tanggal Daftar Ulang</div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <h2 class="h6 fw-bold mb-0">Formulir Daftar Ulang</h2>
                <span class="text-muted-soft small">Isi ukuran seragam dan bukti pembayaran bila ada.</span>
            </div>
            @if ($daftarUlang->status === 'verified')
                <span class="badge text-bg-success"><i class="bi bi-check-circle-fill me-1"></i> Terverifikasi</span>
            @else
                <span class="badge text-bg-warning"><i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi</span>
            @endif
        </div>

        <div class="admin-card-body">
            <form action="{{ route('calon.daftar-ulang.store') }}" method="POST" enctype="multipart/form-data" class="d-grid gap-3">
                @csrf

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="ukuran_seragam" class="form-label fw-semibold">Ukuran Seragam <span class="text-danger">*</span></label>
                        <select id="ukuran_seragam" name="ukuran_seragam" class="form-select @error('ukuran_seragam') is-invalid @enderror" required>
                            <option value="">Pilih ukuran</option>
                            @foreach (['S', 'M', 'L', 'XL', 'XXL'] as $ukuran)
                                <option value="{{ $ukuran }}" @selected(old('ukuran_seragam', $daftarUlang->ukuran_seragam ?? '') === $ukuran)>{{ $ukuran }}</option>
                            @endforeach
                        </select>
                        @error('ukuran_seragam')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="bukti_pembayaran" class="form-label fw-semibold">Bukti Pembayaran / Berkas</label>
                        <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept=".pdf,.jpg,.jpeg,.png"
                               class="form-control @error('bukti_pembayaran') is-invalid @enderror">
                        <div class="form-text">PDF/JPG/PNG, maksimal 2 MB.</div>
                        @error('bukti_pembayaran')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if ($daftarUlang->bukti_pembayaran)
                            <div class="mt-2">
                                <a href="{{ asset('storage/'.$daftarUlang->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> Lihat berkas tersimpan
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <label for="catatan" class="form-label fw-semibold">Catatan</label>
                    <textarea id="catatan" name="catatan" rows="3" class="form-control @error('catatan') is-invalid @enderror"
                              placeholder="Tuliskan catatan untuk panitia (opsional)...">{{ old('catatan', $daftarUlang->catatan ?? '') }}</textarea>
                    @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-between gap-2">
                    <a href="{{ route('calon.dashboard') }}" class="btn btn-outline-dark">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                    </a>
                    <button type="submit" class="btn btn-primary fw-semibold">
                        <i class="bi bi-send me-1"></i> Kirim Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection