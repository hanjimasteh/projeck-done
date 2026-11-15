@extends('layouts.front')

@section('title', 'Daftar Akun - PPDB SMAKNIS')
@section('meta_description', 'Buat akun pendaftar PPDB SMAKNIS untuk memulai proses pendaftaran.')

@section('content')
    <section class="section-padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-9 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-body p-4 p-lg-5">
                            <div class="text-center mb-4">
                                <div class="info-icon mx-auto mb-3"><i class="bi bi-person-plus"></i></div>
                                <span class="section-eyebrow">Akun Calon Siswa</span>
                                <h1 class="h3 fw-bold mt-2 mb-1">Daftar Akun Pendaftar</h1>
                                <p class="text-muted-soft mb-0">
                                    Registrasi hanya untuk calon peserta didik baru.
                                </p>
                            </div>

                            <form action="{{ route('register') }}" method="POST" class="d-grid gap-3">
                                @csrf

                                <div>
                                    <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        value="{{ old('name') }}"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Nama sesuai dokumen"
                                        required
                                        autofocus
                                        autocomplete="name"
                                    >
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row g-3">
                                    <div class="col-12 col-sm-6">
                                        <label for="username" class="form-label fw-semibold">Username</label>
                                        <input
                                            type="text"
                                            id="username"
                                            name="username"
                                            value="{{ old('username') }}"
                                            class="form-control @error('username') is-invalid @enderror"
                                            placeholder="username"
                                            required
                                            autocomplete="username"
                                        >
                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="email" class="form-label fw-semibold">Email</label>
                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            value="{{ old('email') }}"
                                            class="form-control @error('email') is-invalid @enderror"
                                            placeholder="nama@email.com"
                                            required
                                            autocomplete="email"
                                        >
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="password" class="form-label fw-semibold">Password</label>
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Minimal 8 karakter"
                                        required
                                        autocomplete="new-password"
                                    >
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password</label>
                                    <input
                                        type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        class="form-control"
                                        placeholder="Ulangi password"
                                        required
                                        autocomplete="new-password"
                                    >
                                </div>

                                <x-button type="submit" variant="primary" size="lg" icon="person-plus">Daftar Akun</x-button>
                            </form>

                            <hr class="my-4">

                            <p class="text-center text-muted-soft mb-0">
                                Sudah memiliki akun?
                                <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Masuk di sini</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
