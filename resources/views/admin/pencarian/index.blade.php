@extends('layouts.backend')

@section('title', 'Hasil Pencarian')
@section('subtitle', $keyword !== '' ? 'Pencarian untuk "'.$keyword.'"' : 'Ketik nama atau nomor pendaftaran pada kolom pencarian di atas.')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="h6 fw-bold mb-0">{{ $pendaftar->total() }} Pendaftar Ditemukan</h2>
            @if ($keyword !== '')
                <a href="{{ route('admin.pendaftar.index') }}" class="btn btn-sm btn-outline-dark">
                    <i class="bi bi-people me-1"></i> Lihat Semua Pendaftar
                </a>
            @endif
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No. Pendaftaran</th>
                        <th>Nama Lengkap</th>
                        <th>Program Pilihan 1</th>
                        <th style="width: 190px;">Status</th>
                        <th style="width: 150px;">Aksi</th>
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
                                <a href="{{ route('admin.pendaftar.show', $item->id) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="admin-empty">
                                    <i class="bi bi-search fs-3 d-block mb-2"></i>
                                    Tidak ada pendaftar yang cocok dengan kata kunci tersebut.
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
