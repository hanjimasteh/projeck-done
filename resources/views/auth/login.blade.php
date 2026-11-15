@extends('layouts.front')

@section('title', 'Masuk - PPDB SMAKNIS')
@section('meta_description', 'Masuk ke akun PPDB SMAKNIS menggunakan email atau username.')

@section('content')
    <section class="section-padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-body p-4 p-lg-5">
                            <div class="text-center mb-4">
                                <div class="info-icon mx-auto mb-3"><i class="bi bi-person-lock"></i></div>
                                <span class="section-eyebrow">Akun PPDB</span>
                                <h1 class="h3 fw-bold mt-2 mb-1">Masuk ke Akun</h1>
                                <p class="text-muted-soft mb-0">
                                    Gunakan email atau username yang terdaftar.
                                </p>
                            </div>

                            <form action="{{ route('login') }}" method="POST" class="d-grid gap-3">
                                @csrf

                                <div>
                                    <label for="login" class="form-label fw-semibold">Email atau Username</label>
                                    <input
                                        type="text"
                                        id="login"
                                        name="login"
                                        value="{{ old('login') }}"
                                        class="form-control form-control-lg @error('login') is-invalid @enderror"
                                        placeholder="nama@email.com atau username"
                                        required
                                        autofocus
                                        autocomplete="username"
                                    >
                                    @error('login')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="password" class="form-label fw-semibold">Password</label>
                                    </div>
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                                        placeholder="Masukkan password"
                                        required
                                        autocomplete="current-password"
                                    >
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" @checked(old('remember'))>
                                    <label class="form-check-label" for="remember">Ingat saya</label>
                                </div>

                                <x-button type="submit" variant="primary" size="lg" icon="box-arrow-in-right">Masuk</x-button>
                            </form>

                            <hr class="my-4">

                            <p class="text-center text-muted-soft mb-0">
                                Belum memiliki akun?
                                <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Daftar sekarang</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
