@extends('layouts.front')

@section('title', 'Informasi PPDB - PPDB SMAKNIS')
@section('meta_description', 'Informasi kuota program keahlian dan ketentuan PPDB SMAKNIS.')

@section('content')
    <x-hero
        compact
        :badge="$ppdb ? 'Tahun Ajaran '.$ppdb->tahun_ajaran : 'Pendaftaran belum dibuka'"
        title="Informasi PPDB"
        description="Pelajari kuota program keahlian dan ketentuan yang berlaku sebelum mendaftar."
    />

    @if (! $ppdb)
        <x-ppdb-closed />
    @else
        <section class="section-padding pb-0">
            <div class="container">
                <div class="row g-4">
                    <div class="col-12 col-md-6 col-lg-3">
                        <x-info-card icon="calendar-range" title="Periode Pendaftaran">
                            {{ $ppdb->tanggal_mulai->translatedFormat('j M Y') }} &ndash;
                            {{ $ppdb->tanggal_selesai->translatedFormat('j M Y') }}
                        </x-info-card>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <x-info-card icon="diagram-3" title="Program Keahlian" variant="success">
                            {{ $ringkasan['program'] }} program dengan total {{ $ringkasan['kuota'] }} kuota
                        </x-info-card>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <x-info-card icon="clipboard-check" title="Dokumen Wajib" variant="danger">
                            {{ $ringkasan['persyaratan_wajib'] }} dokumen wajib disiapkan
                        </x-info-card>
                    </div>
                </div>
            </div>
        </section>

        <section id="program" class="section-padding">
            <div class="container">
                <div class="mb-4">
                    <span class="section-eyebrow">Program Keahlian</span>
                    <h2 class="section-title h3 mt-2 mb-1">Kuota per Program</h2>
                    <p class="text-muted-soft mb-0">Calon peserta didik dapat memilih program keahlian sesuai minat.</p>
                </div>

                <div class="row g-4">
                    @forelse ($program as $item)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="info-card p-4 h-100">
                                <x-badge variant="primary">{{ $item->kode }}</x-badge>
                                <h3 class="h5 fw-bold mt-3">{{ $item->nama }}</h3>
                                <p class="text-muted-soft small mb-2">Kuota penerimaan</p>
                                <p class="display-6 fw-bold text-primary mb-0">{{ $item->kuota }}</p>
                                <p class="small text-muted-soft mb-0">siswa</p>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info border-0 shadow-sm mb-0">Belum ada data program keahlian.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section id="ketentuan" class="section-padding">
            <div class="container">
                <div class="row g-4 align-items-start">
                    <div class="col-lg-5">
                        <span class="section-eyebrow">Ketentuan</span>
                        <h2 class="section-title h3 mt-2 mb-1">Ketentuan Pendaftaran</h2>
                        <p class="text-muted-soft">
                            Pendaftaran dilakukan secara online. Pastikan seluruh data dan dokumen
                            yang diunggah benar serta dapat dipertanggungjawabkan.
                        </p>
                        <x-button :href="route('persyaratan')" variant="primary" :outline="true" icon="clipboard-check">
                            Lihat persyaratan
                        </x-button>
                    </div>

                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-4">
                                <ul class="list-unstyled d-grid gap-3 mb-0">
                                    <li class="d-flex gap-3">
                                        <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                        <span>Pendaftaran hanya dapat dilakukan melalui akun pendaftar pada website resmi PPDB.</span>
                                    </li>
                                    <li class="d-flex gap-3">
                                        <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                        <span>Setiap pendaftar hanya diperbolehkan memiliki satu nomor pendaftaran pada periode aktif.</span>
                                    </li>
                                    <li class="d-flex gap-3">
                                        <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                        <span>Data yang telah dikirim akan diverifikasi oleh panitia sesuai jadwal yang berlaku.</span>
                                    </li>
                                    <li class="d-flex gap-3">
                                        <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                        <span>Dokumen wajib harus diunggah dalam format PDF, JPG, atau PNG dengan ukuran maksimal 2 MB.</span>
                                    </li>
                                    <li class="d-flex gap-3">
                                        <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                        <span>Hasil penerimaan hanya dapat dilihat setelah pengumuman resmi dipublikasikan.</span>
                                    </li>
                                    <li class="d-flex gap-3">
                                        <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                        <span>Pendaftar yang diterima wajib melakukan daftar ulang sesuai jadwal yang ditetapkan.</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
