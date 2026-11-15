@extends('layouts.calon')

@section('title', 'Profil Saya')
@section('subtitle', 'Kelola data akun dan keamanan akun pendaftar.')

@section('content')
    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="avatar-circle" style="width: 3.25rem; height: 3.25rem; font-size: 1.35rem;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <h2 class="h5 fw-bold mb-1 text-truncate">{{ $user->name }}</h2>
                            <p class="small text-muted-soft mb-0 text-truncate">{{ $user->email }}</p>
                        </div>
                    </div>

                    <ul class="list-unstyled small d-grid gap-3 mb-0">
                        <li class="d-flex justify-content-between gap-3">
                            <span class="text-muted-soft">Username</span>
                            <span class="fw-semibold">{{ $user->username }}</span>
                        </li>
                        <li class="d-flex justify-content-between gap-3">
                            <span class="text-muted-soft">Peran</span>
                            <span class="badge {{ $user->role->badgeClass() }}">{{ $user->role->label() }}</span>
                        </li>
                        <li class="d-flex justify-content-between gap-3">
                            <span class="text-muted-soft">Status</span>
                            <span class="badge {{ $user->status === 'aktif' ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="h4 fw-bold mb-1">Ubah Profil</h2>
                    <p class="text-muted-soft mb-4">
                        Perbarui nama dan email. Kosongkan bagian password jika tidak ingin menggantinya.
                    </p>

                    <form action="{{ route('calon.profil.update') }}" method="POST" class="d-grid gap-4">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name', $user->name) }}"
                                    class="form-control @error('name') is-invalid @enderror"
                                    required
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email', $user->email) }}"
                                    class="form-control @error('email') is-invalid @enderror"
                                    required
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-0">

                        <div>
                            <h3 class="h6 fw-bold mb-1">Ganti Password</h3>
                            <p class="small text-muted-soft mb-3">Isi password saat ini dan password baru untuk mengganti password.</p>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="current_password" class="form-label fw-semibold">Password Saat Ini</label>
                                    <input
                                        type="password"
                                        id="current_password"
                                        name="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        autocomplete="current-password"
                                    >
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="password" class="form-label fw-semibold">Password Baru</label>
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Minimal 8 karakter"
                                        autocomplete="new-password"
                                    >
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password Baru</label>
                                    <input
                                        type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        class="form-control"
                                        autocomplete="new-password"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <x-button type="submit" variant="primary" icon="check2-circle">Simpan Perubahan</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
