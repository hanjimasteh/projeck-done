@extends('layouts.front')

@section('title', 'Jadwal PPDB - PPDB SMAKNIS')
@section('meta_description', 'Jadwal lengkap tahapan PPDB SMAKNIS dari pendaftaran hingga daftar ulang.')

@section('content')
    <x-hero
        compact
        :badge="$ppdb ? 'Tahun Ajaran '.$ppdb->tahun_ajaran : 'Pendaftaran belum dibuka'"
        title="Jadwal PPDB"
        description="Ikuti setiap tahapan pendaftaran sesuai jadwal yang telah ditetapkan panitia."
    />

    @if (! $ppdb)
        <x-ppdb-closed />
    @else
        <section class="section-padding">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-end gap-3 mb-4">
                            <div>
                                <span class="section-eyebrow">Timeline</span>
                                <h2 class="section-title h3 mt-2 mb-1">Tahapan Pendaftaran</h2>
                                <p class="text-muted-soft mb-0">
                                    Periode PPDB: {{ $ppdb->tanggal_mulai->translatedFormat('j F Y') }} &ndash;
                                    {{ $ppdb->tanggal_selesai->translatedFormat('j F Y') }}
                                </p>
                            </div>
                            <x-badge variant="success" pill>
                                <i class="bi bi-broadcast me-1"></i> PPDB Aktif
                            </x-badge>
                        </div>

                        @if ($jadwal->isEmpty())
                            <div class="alert alert-info border-0 shadow-sm">Jadwal PPDB belum tersedia.</div>
                        @else
                            <div class="timeline-ppdb">
                                @foreach ($jadwal as $item)
                                    <div class="timeline-item {{ $item->sedangBerlangsung() ? 'is-current' : ($item->sudahSelesai() ? 'is-done' : '') }}">
                                        <div class="timeline-marker">{{ $item->urutan }}</div>
                                        <div class="card border-0 shadow-sm rounded-4">
                                            <div class="card-body p-4">
                                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-3">
                                                    <div>
                                                        <h3 class="h5 fw-bold mb-1">{{ $item->tahapan }}</h3>
                                                        <p class="text-muted-soft small mb-0">
                                                            <i class="bi bi-calendar3 me-1"></i>
                                                            {{ $item->tanggal_mulai->translatedFormat('j F Y') }} &ndash;
                                                            {{ $item->tanggal_selesai->translatedFormat('j F Y') }}
                                                        </p>
                                                    </div>
                                                    <x-badge :variant="$item->statusBadgeClass()">{{ $item->statusLabel() }}</x-badge>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="alert alert-light border shadow-sm mt-4 mb-0 d-flex gap-2">
                                <i class="bi bi-info-circle text-primary mt-1"></i>
                                <span>Tahapan dengan penanda hijau adalah tahap yang sedang berlangsung saat ini.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
