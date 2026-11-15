@extends('layouts.front')

@section('title', 'Pengumuman PPDB - PPDB SMAKNIS')
@section('meta_description', 'Pengumuman resmi PPDB SMAKNIS dan pengecekan hasil penerimaan menggunakan nomor pendaftaran.')

@section('content')
    <x-hero
        compact
        :badge="$ppdb ? 'Tahun Ajaran '.$ppdb->tahun_ajaran : 'Pendaftaran belum dibuka'"
        title="Pengumuman PPDB"
        description="Pantau pengumuman resmi dan cek hasil penerimaan menggunakan nomor pendaftaran serta tanggal lahir."
    />

    @if (! $ppdb)
        <x-ppdb-closed />
    @else
        <section class="section-padding">
            <div class="container">
                <div class="row g-4 align-items-start">
                    <div class="col-lg-7">
                        <div class="mb-4">
                            <span class="section-eyebrow">Pengumuman Umum</span>
                            <h2 class="section-title h3 mt-2 mb-1">Informasi Resmi</h2>
                            <p class="text-muted-soft mb-0">
                                Pengumuman berikut telah dipublikasikan oleh panitia PPDB.
                            </p>
                        </div>

                        @if ($pengumuman->isEmpty())
                            <div class="alert alert-info border-0 shadow-sm mb-0">
                                <div class="d-flex gap-2">
                                    <i class="bi bi-megaphone mt-1"></i>
                                    <div>
                                        <strong>Hasil penerimaan belum diumumkan.</strong>
                                        <p class="mb-0 small">
                                            Belum ada pengumuman yang dipublikasikan. Silakan pantau halaman ini
                                            secara berkala.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="d-grid gap-3">
                                @foreach ($pengumuman as $item)
                                    <div class="info-card p-4">
                                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-2">
                                            <div>
                                                <h3 class="h6 fw-bold mb-1">Pengumuman Hasil Penerimaan PPDB</h3>
                                                <p class="small text-muted-soft mb-0">
                                                    Tahun Ajaran {{ $ppdb->tahun_ajaran }}
                                                </p>
                                            </div>
                                            <x-badge variant="success">Dipublikasikan</x-badge>
                                        </div>
                                        <hr class="my-3">
                                        <p class="small mb-0">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $item->tanggal_pengumuman->translatedFormat('j F Y, H:i') }} WIB
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                            <div class="alert alert-light border shadow-sm mt-4 mb-0 small">
                                <i class="bi bi-shield-check text-success me-1"></i>
                                Hasil penerimaan bersifat pribadi. Gunakan form di samping untuk melihat hasil Anda.
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-4">
                                <div class="info-icon mb-3"><i class="bi bi-search"></i></div>
                                <h2 class="h5 fw-bold">Cek Hasil Penerimaan</h2>
                                <p class="text-muted-soft small">
                                    Masukkan nomor pendaftaran dan tanggal lahir yang digunakan saat mendaftar.
                                </p>

                                <form action="{{ route('pengumuman.cek') }}" method="POST" class="d-grid gap-3">
                                    @csrf
                                    <div>
                                        <label for="nomor_pendaftaran" class="form-label fw-semibold">Nomor Pendaftaran</label>
                                        <input
                                            type="text"
                                            id="nomor_pendaftaran"
                                            name="nomor_pendaftaran"
                                            value="{{ old('nomor_pendaftaran') }}"
                                            class="form-control @error('nomor_pendaftaran') is-invalid @enderror"
                                            placeholder="Contoh: PPDB-2027-0001"
                                            required
                                        >
                                        @error('nomor_pendaftaran')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="tanggal_lahir" class="form-label fw-semibold">Tanggal Lahir</label>
                                        <input
                                            type="date"
                                            id="tanggal_lahir"
                                            name="tanggal_lahir"
                                            value="{{ old('tanggal_lahir') }}"
                                            class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                            required
                                        >
                                        @error('tanggal_lahir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <x-button type="submit" variant="primary" icon="search">Cek Hasil</x-button>
                                </form>

                                @if ($hasilCek)
                                    @php($diterima = $hasilCek['hasil'] === \App\Models\Pengumuman::HASIL_DITERIMA)
                                    <div class="alert {{ $diterima ? 'alert-success' : 'alert-danger' }} border-0 shadow-sm mt-4 mb-0">
                                        <h3 class="h6 fw-bold mb-2">
                                            {{ $diterima ? 'Selamat, Anda dinyatakan diterima!' : 'Mohon maaf, Anda belum diterima.' }}
                                        </h3>
                                        <p class="small mb-1">Nomor: <strong>{{ $hasilCek['nomor_pendaftaran'] }}</strong></p>
                                        <p class="small mb-1">Nama: <strong>{{ $hasilCek['nama'] }}</strong></p>
                                        @if ($hasilCek['keterangan'])
                                            <p class="small mb-1">{{ $hasilCek['keterangan'] }}</p>
                                        @endif
                                        @if ($hasilCek['tanggal'])
                                            <p class="small mb-0">
                                                Diumumkan:
                                                {{ $hasilCek['tanggal']->translatedFormat('j F Y, H:i') }} WIB
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
