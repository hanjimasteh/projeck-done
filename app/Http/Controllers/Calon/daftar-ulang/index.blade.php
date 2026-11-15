@extends('layouts.calon')

@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-dark">Formulir Daftar Ulang</h3>
    <p class="text-muted">Lengkapi data daftar ulang Anda sebagai konfirmasi resmi bergabung di SMK Nurul Islam.</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-file-pen text-primary me-2"></i>Konfirmasi Daftar Ulang</h5>
                
                <form action="{{ route('calon.daftar-ulang.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ukuran Seragam Sekolah</label>
                        <select name="ukuran_seragam" class="form-select" required>
                            <option value="">-- Pilih Ukuran Seragam --</option>
                            <option value="S" {{ optional($pendaftaran->daftarUlang)->ukuran_seragam == 'S' ? 'selected' : '' }}>S (Small)</option>
                            <option value="M" {{ optional($pendaftaran->daftarUlang)->ukuran_seragam == 'M' ? 'selected' : '' }}>M (Medium)</option>
                            <option value="L" {{ optional($pendaftaran->daftarUlang)->ukuran_seragam == 'L' ? 'selected' : '' }}>L (Large)</option>
                            <option value="XL" {{ optional($pendaftaran->daftarUlang)->ukuran_seragam == 'XL' ? 'selected' : '' }}>XL (Extra Large)</option>
                            <option value="XXL" {{ optional($pendaftaran->daftarUlang)->ukuran_seragam == 'XXL' ? 'selected' : '' }}>XXL (Double Extra Large)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Unggah Bukti Daftar Ulang / Pembayaran (Jika Ada)</label>
                        <input type="file" name="bukti_pembayaran" class="form-control">
                        <div class="form-text">Format: PDF, JPG, PNG (Maks. 2MB).</div>
                        @if(optional($pendaftaran->daftarUlang)->bukti_pembayaran)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $pendaftaran->daftarUlang->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye me-1"></i> Lihat File yang Telah Diunggah
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Catatan Tambahan (Opsional)</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tuliskan catatan jika ada hal yang ingin disampaikan...">{{ old('catatan', optional($pendaftaran->daftarUlang)->catatan) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                        <i class="fa-solid fa-paper-plane me-2"></i> Kirim Konfirmasi Daftar Ulang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-circle-info text-primary me-2"></i>Status Daftar Ulang</h5>
                
                <div class="mb-3">
                    <span class="text-muted small">Status Saat Ini:</span>
                    <div class="mt-1">
                        @php $statusDU = optional($pendaftaran->daftarUlang)->status ?? 'belum_konfirmasi'; @endphp
                        @if($statusDU == 'verified')
                            <span class="badge bg-success px-3 py-2 w-100">Daftar Ulang Terverifikasi</span>
                        @elseif($statusDU == 'pending')
                            <span class="badge bg-warning text-dark px-3 py-2 w-100">Menunggu Verifikasi Panitia</span>
                        @else
                            <span class="badge bg-secondary px-3 py-2 w-100">Belum Mengisi Formulir</span>
                        @endif
                    </div>
                </div>

                <div class="alert alert-info border-0 rounded-3 mb-0">
                    <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-lightbulb me-1"></i> Informasi Penting:</h6>
                    <p class="small text-muted mb-0">Pastikan ukuran seragam yang kamu pilih sudah benar. Jika ada kendala administrasi, silakan hubungi panitia PPDB SMK Nurul Islam melalui kontak sekolah.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection