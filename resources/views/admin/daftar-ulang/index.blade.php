@extends('layouts.backend')

@section('title', 'Daftar Ulang')
@section('subtitle', 'Verifikasi data daftar ulang dan ukuran seragam calon siswa.')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="h6 fw-bold mb-0">Data Daftar Ulang</h2>
            <span class="text-muted-soft small">Total {{ $daftarUlang->total() }} siswa</span>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No. Pendaftaran</th>
                        <th>Nama Lengkap</th>
                        <th>Program</th>
                        <th style="width: 150px;">Ukuran Seragam</th>
                        <th style="width: 130px;">Bukti Bayar</th>
                        <th style="width: 130px;">Status</th>
                        <th style="width: 220px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarUlang as $du)
                        <tr>
                            <td class="fw-semibold text-primary">{{ $du->pendaftaran?->nomor_pendaftaran ?? '-' }}</td>
                            <td>{{ $du->pendaftaran?->calonSiswa?->nama_lengkap ?? '-' }}</td>
                            <td>{{ $du->pendaftaran?->programPilihan1?->nama ?? '-' }}</td>
                            <td><span class="badge text-bg-light border">{{ $du->ukuran_seragam ?? 'Belum diisi' }}</span></td>
                            <td>
                                @if ($du->bukti_pembayaran)
                                    <a href="{{ asset('storage/'.$du->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-outline-primary fw-semibold">
                                        <i class="bi bi-eye me-1"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-muted-soft small">Tidak ada</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ ($du->status ?? 'pending') === 'verified' ? 'text-bg-success' : 'text-bg-warning' }}">
                                    {{ ($du->status ?? 'pending') === 'verified' ? 'Terverifikasi' : 'Pending' }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('admin.daftar-ulang.update', $du->id) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    <select name="status" class="form-select form-select-sm" required>
                                        <option value="pending" @selected(($du->status ?? 'pending') === 'pending')>Pending</option>
                                        <option value="verified" @selected(($du->status ?? 'pending') === 'verified')>Verifikasi</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary fw-semibold">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="admin-empty">
                                    <i class="bi bi-clipboard-check fs-3 d-block mb-2"></i>
                                    Belum ada data siswa yang melakukan daftar ulang.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($daftarUlang->hasPages())
            <div class="p-3 border-top">{{ $daftarUlang->links() }}</div>
        @endif
    </div>
@endsection
