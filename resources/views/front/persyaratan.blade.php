@extends('layouts.front')

@section('title', 'Persyaratan PPDB - PPDB SMAKNIS')
@section('meta_description', 'Daftar dokumen wajib dan opsional, format, serta ukuran maksimal berkas PPDB SMAKNIS.')

@section('content')
    <x-hero
        compact
        :badge="$ppdb ? 'Tahun Ajaran '.$ppdb->tahun_ajaran : 'Pendaftaran belum dibuka'"
        title="Persyaratan Pendaftaran"
        description="Siapkan dokumen berikut sebelum memulai pendaftaran agar proses verifikasi berjalan lancar."
    />

    @if (! $ppdb)
        <x-ppdb-closed />
    @else
        <section class="section-padding">
            <div class="container">
                <div class="row g-4 align-items-start mb-4">
                    <div class="col-lg-8">
                        <span class="section-eyebrow">Dokumen Unggahan</span>
                        <h2 class="section-title h3 mt-2 mb-1">Berkas yang Perlu Diunggah</h2>
                        <p class="text-muted-soft mb-0">
                            Seluruh berkas diunggah melalui akun pendaftar. Pastikan hasil pindai terbaca jelas.
                        </p>
                    </div>
                    <div class="col-lg-4">
                        <div class="alert alert-info border-0 shadow-sm mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            Format: PDF, JPG, atau PNG. Ukuran maksimal 2 MB per berkas.
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    @forelse ($jenisDokumen as $item)
                        @php
                            $format = collect(explode(',', $item->format_file))
                                ->map(fn ($file) => strtoupper(trim($file)))
                                ->join(', ');
                            $ukuran = $item->ukuran_maks_kb >= 1024
                                ? round($item->ukuran_maks_kb / 1024).' MB'
                                : $item->ukuran_maks_kb.' KB';
                            $ikon = match ($item->kategori) {
                                'Identitas' => 'person-vcard',
                                'Akademik' => 'journal-bookmark',
                                'Prestasi' => 'trophy',
                                default => 'file-earmark-text',
                            };
                        @endphp
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="info-card p-4 h-100">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                    <div class="info-icon"><i class="bi bi-{{ $ikon }}"></i></div>
                                    <x-badge :variant="$item->wajib ? 'danger' : 'secondary'">
                                        {{ $item->wajib ? 'Wajib' : 'Opsional' }}
                                    </x-badge>
                                </div>
                                <h3 class="h5 fw-bold">{{ $item->nama }}</h3>
                                <p class="text-muted-soft small mb-3">Kategori: {{ $item->kategori }}</p>
                                <ul class="list-unstyled small text-muted-soft d-grid gap-2 mb-0">
                                    <li><i class="bi bi-filetype-pdf me-1"></i> Format: {{ $format }}</li>
                                    <li><i class="bi bi-hdd me-1"></i> Maksimal: {{ $ukuran }}</li>
                                </ul>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info border-0 shadow-sm mb-0">Belum ada data jenis dokumen.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="section-padding bg-white">
            <div class="container">
                <div class="mb-4">
                    <span class="section-eyebrow">Persyaratan Umum</span>
                    <h2 class="section-title h3 mt-2 mb-1">Ketentuan Dokumen</h2>
                    <p class="text-muted-soft mb-0">Perhatikan status wajib atau opsional pada setiap persyaratan.</p>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse ($persyaratan as $item)
                                <div class="list-group-item d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 p-4">
                                    <div>
                                        <h3 class="h6 fw-bold mb-1">{{ $item->nama }}</h3>
                                        <p class="small text-muted-soft mb-1">{{ $item->deskripsi }}</p>
                                        <small class="text-muted-soft">
                                            <i class="bi bi-mortarboard me-1"></i>
                                            Berlaku untuk semua program keahlian
                                        </small>
                                    </div>
                                    <x-badge :variant="$item->wajib ? 'danger' : 'secondary'">
                                        {{ $item->wajib ? 'Wajib' : 'Opsional' }}
                                    </x-badge>
                                </div>
                            @empty
                                <div class="list-group-item p-4 text-center text-muted-soft">
                                    Belum ada data persyaratan.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
