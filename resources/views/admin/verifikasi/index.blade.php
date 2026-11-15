@extends('layouts.backend')

@section('title', 'Verifikasi')
@section('subtitle', 'Periksa berkas pendaftar. Kuota dialokasikan otomatis saat berkas diverifikasi.')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="h6 fw-bold mb-0">Daftar Pendaftar</h2>
            <span class="text-muted-soft small">Total {{ $pendaftar->total() }} pendaftar</span>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No. Pendaftaran</th>
                        <th>Nama Lengkap</th>
                        <th>Program Pilihan 1</th>
                        <th style="width: 190px;">Status</th>
                        <th style="width: 170px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendaftar as $item)
                        <tr>
                            <td class="fw-semibold text-primary">{{ $item->nomor_pendaftaran ?? '-' }}</td>
                            <td>{{ $item->calonSiswa?->nama_lengkap ?? '-' }}</td>
                            <td>{{ $item->programPilihan1?->nama ?? '-' }}</td>
                            <td><x-status-badge :status="$item->status" /></td>
                            <td>
                                <a href="{{ route('admin.verifikasi.show', $item->id) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                    <i class="bi bi-file-earmark-check me-1"></i> Verifikasi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="admin-empty">
                                    <i class="bi bi-file-earmark-check fs-3 d-block mb-2"></i>
                                    Belum ada pendaftar yang menunggu verifikasi.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pendaftar->hasPages())
            <div class="p-3 border-top">{{ $pendaftar->links() }}</div>
        @endif
    </div>
@endsection
