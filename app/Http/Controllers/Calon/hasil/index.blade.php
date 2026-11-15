@extends('layouts.calon') <!-- Sesuaikan dengan layout calon siswa kamu -->

@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-dark">Hasil Seleksi PPDB</h3>
    <p class="text-muted">Informasi status kelulusan seleksi penerimaan peserta didik baru.</p>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4 text-center py-5">
        @if(!$pendaftaran)
            <div class="py-4">
                <i class="fa-solid fa-triangle-exclamation text-warning display-4 mb-3"></i>
                <h5>Anda Belum Melakukan Pendaftaran</h5>
                <p class="text-muted">Silakan lengkapi formulir pendaftaran terlebih dahulu pada menu Pendaftaran.</p>
                <a href="{{ route('calon.formulir') }}" class="btn btn-primary fw-bold mt-2">Isi Formulir Sekarang</a>
            </div>
        @elseif($pendaftaran->status == 'pending' || $pendaftaran->status == 'verified')
            <div class="py-4">
                <i class="fa-solid fa-hourglass-half text-primary display-4 mb-3"></i>
                <h4 class="fw-bold text-dark">Seleksi Sedang Berproses</h4>
                <p class="text-muted">Berkas Anda sudah masuk dan sedang menunggu proses seleksi akhir oleh panitia.</p>
                <span class="badge bg-info text-dark px-3 py-2 fs-6">Status: Terverifikasi / Menunggu Seleksi</span>
            </div>
        @else
            <!-- Jika sudah diputuskan (Diterima / Tidak Diterima) -->
            <div class="py-3">
                @if(optional($pendaftaran->pengumuman)->hasil == 'diterima' || $pendaftaran->status == 'selected')
                    <div class="mb-3">
                        <span class="display-1 text-success"><i class="fa-solid fa-circle-check"></i></span>
                    </div>
                    <h2 class="fw-bold text-success mb-2">SELAMAT, ANDA DINYATAKAN DITERIMA!</h2>
                    <p class="text-muted mb-4">Silakan melanjutkan ke tahap daftar ulang sesuai jadwal yang telah ditentukan.</p>
                    
                    <div class="card bg-light border-0 mx-auto rounded-3 p-4 text-start mb-4" style="max-width: 600px;">
                        <h5 class="fw-bold text-dark mb-3">Detail Kelulusan:</h5>
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted" style="width: 40%;">No. Pendaftaran</td>
                                <td class="fw-bold">: {{ $pendaftaran->nomor_pendaftaran }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jurusan Diterima</td>
                                <td class="fw-bold">: {{ optional($pendaftaran->jurusan)->nama_jurusan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Keterangan / Catatan</td>
                                <td class="fw-bold">: {{ optional($pendaftaran->pengumuman)->keterangan ?? 'Memenuhi seluruh kriteria seleksi.' }}</td>
                            </tr>
                        </table>
                    </div>

                    <a href="{{ route('calon.daftar-ulang') }}" class="btn btn-success fw-bold px-4 py-2">
                        <i class="fa-solid fa-file-pen me-2"></i> Lanjut Daftar Ulang
                    </a>
                @else
                    <div class="mb-3">
                        <span class="display-1 text-danger"><i class="fa-solid fa-circle-xmark"></i></span>
                    </div>
                    <h2 class="fw-bold text-danger mb-2">MOHON MAAF, ANDA BELUM DITERIMA</h2>
                    <p class="text-muted mb-4">Terima kasih telah mendaftar di SMK Nurul Islam. Tetap semangat dan jangan berkecil hati.</p>
                    
                    <div class="card bg-light border-0 mx-auto rounded-3 p-4 text-start" style="max-width: 600px;">
                        <p class="text-muted mb-0"><strong>Catatan Panitia:</strong> {{ optional($pendaftaran->pengumuman)->keterangan ?? 'Kuota terpenuhi atau belum memenuhi ambang batas nilai seleksi.' }}</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection