@extends('layouts.front')

@section('title', 'PPDB SMAKNIS - Penerimaan Peserta Didik Baru Online')
@section('meta_description', 'Daftar akun, pilih jurusan favorit, unggah berkas, lalu pantau status verifikasi dan pengumuman dalam satu tempat.')

@section('content')
    <x-hero
        :badge="$ppdb ? 'Pendaftaran TA '.$ppdb->tahun_ajaran.' Dibuka' : 'Pendaftaran belum dibuka'"
        title="Penerimaan Peserta Didik Baru"
        accent="Online"
        description="Daftar akun dengan email sendiri, pilih jurusan favorit, unggah berkas, lalu pantau status verifikasi dan pengumuman dalam satu tempat."
    >
        <x-slot:actions>
            @auth
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('calon.dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-speedometer2 me-1"></i> Buka Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus me-1"></i> Buat Akun Siswa
                </a>
                <a href="{{ route('jurusan') }}" class="btn btn-outline-dark">
                    <i class="bi bi-grid-3x3-gap me-1"></i> Lihat Jurusan
                </a>
            @endauth
        </x-slot:actions>

        <x-slot:stats>
            <div class="stat-tile">
                <div class="stat-value">{{ $jumlahJurusan }}</div>
                <div class="stat-label">Jurusan</div>
            </div>
            <div class="stat-tile">
                <div class="stat-value">{{ number_format($totalKuota, 0, ',', '.') }}</div>
                <div class="stat-label">Total kuota</div>
            </div>
            <div class="stat-tile">
                <div class="stat-value">{{ $jumlahPendaftar }}</div>
                <div class="stat-label">Pendaftar</div>
            </div>
        </x-slot:stats>
    </x-hero>

    @if (! $ppdb)
        <x-ppdb-closed />
    @else
        <section class="section-padding">
            <div class="container">
                <div class="text-center mx-auto mb-5" style="max-width: 42rem;">
                    <span class="section-eyebrow">Program Keahlian</span>
                    <h2 class="section-title h3 mt-2 mb-2">Pilihan Program Tersedia</h2>
                    <p class="text-muted-soft mb-0">
                        Setiap program memiliki kuota penerimaan yang terbatas.
                    </p>
                </div>

                <div class="row g-4">
                    <div class="col-12 col-md-6 col-lg-4">
                        <x-info-card icon="diagram-3" title="Program Keahlian" variant="success">
                            <p class="mb-2">{{ $ringkasan['program'] }} program dengan total {{ $ringkasan['kuota'] }} kuota.</p>
                            <span class="fw-semibold text-success">{{ $program->pluck('kode')->join(', ') }}</span>
                        </x-info-card>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <x-info-card icon="calendar3" title="Jadwal PPDB" variant="warning">
                            <p class="mb-2">
                                @if ($tahapAktif)
                                    Tahap <strong>{{ $tahapAktif->tahapan }}</strong> sedang berlangsung
                                    ({{ $tahapAktif->tanggal_mulai->translatedFormat('j M') }}
                                    &ndash; {{ $tahapAktif->tanggal_selesai->translatedFormat('j M Y') }}).
                                @else
                                    Pendaftaran dibuka
                                    {{ $ppdb->tanggal_mulai->translatedFormat('j M') }}
                                    &ndash; {{ $ppdb->tanggal_selesai->translatedFormat('j M Y') }}.
                                @endif
                            </p>
                            <x-button :href="route('jadwal')" variant="primary" :outline="true" icon="calendar3">Lihat jadwal</x-button>
                        </x-info-card>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-padding bg-white">
            <div class="container">
                <div class="text-center mx-auto mb-5" style="max-width: 42rem;">
                    <span class="section-eyebrow">Jurusan</span>
                    <h2 class="section-title h3 mt-2 mb-2">Jurusan yang Tersedia</h2>
                    <p class="text-muted-soft mb-0">
                        Pilih program keahlian yang kamu incar, lalu isi formulir pendaftaran pada tahap berikutnya.
                    </p>
                </div>

                <div class="row g-4">
                    @forelse ($program as $item)
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="info-card p-4 h-100 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                    <div class="info-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
                                    <x-badge variant="primary">{{ $item->kode }}</x-badge>
                                </div>
                                <h3 class="h6 fw-bold mb-0">{{ $item->nama }}</h3>
                                <div class="mt-auto d-flex align-items-baseline gap-2 pt-3">
                                    <span class="h4 fw-bold text-primary mb-0">{{ $item->kuota }}</span>
                                    <span class="small text-muted-soft">kuota</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info border-0 shadow-sm mb-0">Belum ada program keahlian yang tersedia.</div>
                        </div>
                    @endforelse
                </div>

                <div class="text-center mt-5">
                    <x-button :href="route('jurusan')" variant="primary" :outline="true" icon="arrow-right">Lihat semua jurusan</x-button>
                </div>
            </div>
        </section>

        <section class="section-padding">
            <div class="container">
                <div class="text-center mx-auto mb-5" style="max-width: 42rem;">
                    <span class="section-eyebrow">Jadwal</span>
                    <h2 class="section-title h3 mt-2 mb-2">Tahapan PPDB</h2>
                    <p class="text-muted-soft mb-0">Pantau setiap tahapan agar tidak melewatkan waktu penting.</p>
                </div>

                <div class="row g-4">
                    @forelse ($jadwal as $item)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="info-card p-4 h-100">
                                <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                                    <span class="badge text-bg-light border">Tahap {{ $item->urutan }}</span>
                                    <x-badge :variant="$item->statusBadgeClass()">{{ $item->statusLabel() }}</x-badge>
                                </div>
                                <h3 class="h5 fw-bold">{{ $item->tahapan }}</h3>
                                <p class="text-muted-soft small mb-0">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $item->tanggal_mulai->translatedFormat('j M Y') }} &ndash;
                                    {{ $item->tanggal_selesai->translatedFormat('j M Y') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info border-0 shadow-sm mb-0">Jadwal PPDB belum tersedia.</div>
                        </div>
                    @endforelse
                </div>

                @if ($tahapAktif)
                    <div class="alert alert-success border-0 shadow-sm mt-4 mb-0 d-flex gap-2">
                        <i class="bi bi-broadcast mt-1"></i>
                        <div>
                            Tahap <strong>{{ $tahapAktif->tahapan }}</strong> sedang berlangsung.
                            {{ $tahapAktif->tanggal_mulai->translatedFormat('j M Y') }} &ndash;
                            {{ $tahapAktif->tanggal_selesai->translatedFormat('j M Y') }}.
                        </div>
                    </div>
                @endif

                <div class="text-center mt-4">
                    <x-button :href="route('jadwal')" variant="primary" :outline="true" icon="calendar3">Lihat semua jadwal</x-button>
                </div>
            </div>
        </section>

        <section class="section-padding bg-white">
            <div class="container">
                <div class="text-center mx-auto mb-5" style="max-width: 42rem;">
                    <span class="section-eyebrow">Alur</span>
                    <h2 class="section-title h3 mt-2 mb-2">Dari Pendaftaran hingga Pengumuman</h2>
                    <p class="text-muted-soft mb-0">Empat tahap utama yang akan dilalui setiap pendaftar.</p>
                </div>

                @php
                    $langkahSingkat = [
                        ['icon' => 'person-plus', 'title' => 'Daftar & Isi Formulir', 'description' => 'Buat akun lalu lengkapi data diri dan pilihan program.'],
                        ['icon' => 'cloud-arrow-up', 'title' => 'Unggah Berkas', 'description' => 'Unggah berkas pendukung melalui akun pendaftar.'],
                        ['icon' => 'clipboard-check', 'title' => 'Verifikasi & Penetapan Kuota', 'description' => 'Panitia memverifikasi berkas, lalu kuota menentukan siapa yang diterima.'],
                        ['icon' => 'megaphone', 'title' => 'Pengumuman Hasil Penerimaan', 'description' => 'Lihat hasil penerimaan yang telah dipublikasikan.'],
                    ];
                @endphp

                <div class="row g-4">
                    @foreach ($langkahSingkat as $langkah)
                        <div class="col-12 col-md-6 col-lg-3">
                            <x-info-card :icon="$langkah['icon']" :title="$langkah['title']">
                                {{ $langkah['description'] }}
                            </x-info-card>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-5">
                    <x-button :href="route('alur')" variant="primary" :outline="true" icon="arrow-right">Lihat alur lengkap</x-button>
                </div>
            </div>
        </section>

        <section class="section-padding">
            <div class="container">
                <div class="text-center mx-auto mb-5" style="max-width: 42rem;">
                    <span class="section-eyebrow">Informasi</span>
                    <h2 class="section-title h3 mt-2 mb-2">Informasi PPDB Terbaru</h2>
                    <p class="text-muted-soft mb-0">Pengumuman dan informasi resmi yang dipublikasikan panitia.</p>
                </div>

                <div class="row g-4">
                    @forelse ($informasi as $item)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="info-card p-4 h-100">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                    <div class="info-icon"><i class="bi bi-megaphone-fill"></i></div>
                                    <x-badge variant="success">Terbit</x-badge>
                                </div>
                                <h3 class="h5 fw-bold mb-2">{{ $item->judul }}</h3>
                                <p class="text-muted-soft small mb-3">{{ \Illuminate\Support\Str::limit($item->isi_konten, 140) }}</p>
                                <p class="small mb-0">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $item->tanggal?->translatedFormat('j F Y') ?? $item->created_at->translatedFormat('j F Y') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info border-0 shadow-sm mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Belum ada informasi yang dipublikasikan.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="section-padding bg-white">
            <div class="container">
                <div class="text-center mx-auto mb-5" style="max-width: 42rem;">
                    <span class="section-eyebrow">Pengumuman</span>
                    <h2 class="section-title h3 mt-2 mb-2">Pengumuman Terbaru</h2>
                    <p class="text-muted-soft mb-0">Informasi hasil penerimaan yang sudah dipublikasikan.</p>
                </div>

                <div class="row g-4">
                    @forelse ($pengumuman as $item)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="info-card p-4 h-100">
                                <x-badge variant="success">Dipublikasikan</x-badge>
                                <h3 class="h6 fw-bold mt-3">Pengumuman Hasil Penerimaan</h3>
                                <p class="small text-muted-soft mb-2">Tahun Ajaran {{ $ppdb->tahun_ajaran }}</p>
                                <p class="small mb-0">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $item->tanggal_pengumuman->translatedFormat('j F Y, H:i') }} WIB
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info border-0 shadow-sm mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Belum ada pengumuman hasil penerimaan yang dipublikasikan.
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="text-center mt-5">
                    <x-button :href="route('pengumuman')" variant="primary" :outline="true" icon="megaphone">Buka pengumuman</x-button>
                </div>
            </div>
        </section>
    @endif
@endsection
