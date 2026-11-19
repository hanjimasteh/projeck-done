@extends('layouts.front')

@section('title', 'Jurusan - PPDB SMAKNIS')
@section('meta_description', 'Daftar program keahlian/jurusan yang dibuka pada PPDB SMAKNIS beserta kuota reception.')

@section('content')
    <x-hero
        compact
        :badge="$ppdb ? 'Tahun Ajaran '.$ppdb->tahun_ajaran : 'Pendaftaran belum dibuka'"
        title="Program Jurusan"
        description="Pilih program keahlian yang ingin kamu daftarkan, lalu lengkapi formulir pendaftaran pada tahap berikutnya."
    />

    @if (! $ppdb)
        <x-ppdb-closed />
    @else
        <section class="section-padding">
            <div class="container">
                <div class="text-center mx-auto mb-5" style="max-width: 42rem;">
                    <span class="section-eyebrow">Pilihan Program</span>
                    <h2 class="section-title h3 mt-2 mb-2">Jurusan yang Tersedia</h2>
                    <p class="text-muted-soft mb-0">
                        {{ $ringkasan['program'] }} program keahlian dengan total {{ $ringkasan['kuota'] }} kuota penerimaan.
                    </p>
                </div>

                <div class="row g-4">
                    @forelse ($program as $item)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="info-card p-4 h-100 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                    <img src="{{ asset('images/jurusan/'.$item->kode.'.svg') }}" alt="Logo {{ $item->kode }}" class="info-icon" style="object-fit: cover;">
                                    <x-badge variant="primary">{{ $item->kode }}</x-badge>
                                </div>
                                <h3 class="h5 fw-bold mb-2">{{ $item->nama }}</h3>
                                <div class="mt-auto d-flex align-items-baseline gap-2 pt-3">
                                    <span class="display-6 fw-bold text-primary mb-0">{{ $item->kuota }}</span>
                                    <span class="small text-muted-soft">kuota siswa</span>
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
                    <x-button :href="route('register')" variant="primary" icon="person-plus">Buat Akun Siswa</x-button>
                </div>
            </div>
        </section>
    @endif
@endsection
