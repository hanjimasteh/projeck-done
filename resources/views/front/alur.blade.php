@extends('layouts.front')

@section('title', 'Alur Pendaftaran - PPDB SMAKNIS')
@section('meta_description', 'Alur pendaftaran PPDB SMAKNIS dari pembuatan akun hingga daftar ulang.')

@section('content')
    <x-hero
        compact
        :badge="$ppdb ? 'Tahun Ajaran '.$ppdb->tahun_ajaran : 'Pendaftaran belum dibuka'"
        title="Alur Pendaftaran"
        description="Ikuti delapan langkah berikut agar proses pendaftaran berjalan lancar dari awal hingga daftar ulang."
    />

    @if (! $ppdb)
        <x-ppdb-closed />
    @else
        <section class="section-padding">
            <div class="container">
                <div class="text-center mb-4">
                    <span class="section-eyebrow">Langkah demi Langkah</span>
                    <h2 class="section-title h3 mt-2 mb-1">Proses Pendaftaran PPDB</h2>
                    <p class="text-muted-soft mb-0">Seluruh proses dilakukan secara online melalui akun pendaftar.</p>
                </div>

                <div class="row g-4">
                    @foreach ($langkah as $index => $item)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="info-card p-4 h-100 position-relative">
                                <span class="step-number">{{ $index + 1 }}</span>
                                <div class="info-icon mb-3"><i class="bi bi-{{ $item['icon'] }}"></i></div>
                                <h3 class="h5 fw-bold">{{ $item['title'] }}</h3>
                                <p class="text-muted-soft mb-0">{{ $item['description'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="card border-0 shadow-sm rounded-4 mt-5">
                    <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h3 class="h5 fw-bold mb-1">Siap memulai pendaftaran?</h3>
                            <p class="text-muted-soft mb-0">Buat akun terlebih dahulu, lalu lengkapi formulir pendaftaran.</p>
                        </div>
                        <x-button :href="route('register')" variant="primary" icon="person-plus">Daftar Akun</x-button>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
