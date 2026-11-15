@extends('layouts.backend')

@section('title', 'Pengumuman')
@section('subtitle', 'Hasil penerimaan yang terbit otomatis mengikuti ketersediaan kuota.')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="h6 fw-bold mb-0">Daftar Pengumuman</h2>
            <span class="text-muted-soft small">
                @if ($ppdb) TA {{ $ppdb->tahun_ajaran }} @else Tanpa periode aktif @endif
            </span>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No. Pendaftaran</th>
                        <th>Nama Lengkap</th>
                        <th>Program</th>
                        <th style="width: 150px;">Hasil</th>
                        <th style="width: 170px;">Diumumkan</th>
                        <th style="width: 130px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengumuman as $item)
                        <tr>
                            <td class="fw-semibold text-primary">{{ $item->pendaftaran?->nomor_pendaftaran ?? '-' }}</td>
                            <td>{{ $item->pendaftaran?->calonSiswa?->nama_lengkap ?? '-' }}</td>
                            <td>{{ $item->pendaftaran?->programPilihan1?->nama ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $item->hasil === \App\Models\Pengumuman::HASIL_DITERIMA ? 'text-bg-success' : 'text-bg-danger' }}">
                                    {{ $item->hasil === \App\Models\Pengumuman::HASIL_DITERIMA ? 'Diterima' : 'Tidak Diterima' }}
                                </span>
                            </td>
                            <td>{{ $item->tanggal_pengumuman?->translatedFormat('d M Y H:i') ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $item->dipublikasikan ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $item->dipublikasikan ? 'Terbit' : 'Draf' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="admin-empty">
                                    <i class="bi bi-megaphone fs-3 d-block mb-2"></i>
                                    Belum ada pengumuman. Pengumuman terbit otomatis saat pendaftar diterima sesuai kuota.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pengumuman->hasPages())
            <div class="p-3 border-top">{{ $pengumuman->links() }}</div>
        @endif
    </div>
@endsection
